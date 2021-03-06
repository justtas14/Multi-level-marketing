<?php

namespace App\Controller\Web;

use App\CustomNormalizer\ConfigurationNormalizer;
use App\Entity\Associate;
use App\Entity\Configuration;
use App\Entity\Invitation;
use App\Entity\User;
use App\Filter\AssociateFilter;
use App\Form\InvitationType;
use App\Form\NewPasswordType;
use App\Form\ResetPasswordType;
use App\Form\UserRegistrationType;
use App\Repository\AssociateRepository;
use App\Service\AssociateManager;
use App\Service\BlacklistManager;
use App\Service\ConfigurationManager;
use App\Service\InvitationManager;
use App\Service\RecaptchaManager;
use App\Service\ResetPasswordManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use PlumTreeSystems\UserBundle\Service\JWTManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return RedirectResponse|void
     */
    public function home()
    {
        return $this->redirectToRoute('authentication');
    }

    /**
     * @Route("/authenticateFlow", name="authentication")
     * @param string $clientId
     * @param Request $request
     * @param JWTManager $jwtManager
     * @return JsonResponse|RedirectResponse
     */
    public function authenticateFlow(string $clientId, Request $request, JWTManager $jwtManager)
    {
        /** @var User $user */
        $user = $this->getUser();
        $redirectUri= $request->query->get('redirect_uri');
        $frontClientId= $request->query->get('client_id');
        if (!$redirectUri) {
            $redirectUri = $this->get('session')->get($redirectUri);
        } else {
            $this->get('session')->set('redirect_uri', $redirectUri);
        }
        if ($user) {
            if ($clientId !== $frontClientId) {
                throw new HttpException(403, 'Cannot access to server');
            }
            $token = $jwtManager->createToken($user);
            return $this->redirect($redirectUri.'?token='.$token);
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/authenticateLogout", name="authenticationLogout")
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticateLogout(Request $request)
    {
        $redirectUri = $request->query->get('redirect_uri');

        if (!$this->get('session')->get($redirectUri)) {
            $this->get('session')->set('redirect_uri', $redirectUri);
        }

        return $this->redirectToRoute('logout');
    }

    /**
     * @OA\Get(
     *     path="/api/configuration",
     *     @OA\Response(response="200",
     *     description="Returns configuration object)
     * )
     */

    /**
     * @Rest\Get("/api/configuration", name="configuration")
     * @param Request $request
     * @param ConfigurationNormalizer $configurationNormalizer
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function configuration(
        Request $request,
        ConfigurationNormalizer $configurationNormalizer
    ) {
        $em = $this->getDoctrine()->getManager();

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $configurationSerializer = new Serializer([
            new DateTimeNormalizer('Y-m-d h:i:s'),
            $configurationNormalizer, new JsonEncoder()
        ]);

        $serializedConfiguration = $configurationSerializer->normalize(
            $configuration,
            null,
            ['attributes' => ['termsOfServices', 'mainLogo', 'landingContent']]
        );


        $payload = [
            'configuration' => $serializedConfiguration,
        ];

        return new JsonResponse($payload, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/setCookie", name="setCookie")
     * @param Request $request
     * @return Response
     */
    public function setCookies(Request $request)
    {
        /** @var User $user */
        $token = $request->request->all()['token'];
        $response = new Response('ok');
        $cookie = new Cookie('authToken', $token);
        $response->headers->setCookie($cookie);
        $response->headers->set('Access-Control-Allow-Credentials', 'True');
        return $response;
    }

    /**
     * @Route("/api/unsetCookie", name="unsetCookie")
     * @return Response
     */
    public function unsetCookies()
    {
        $response = new Response('ok');
        $response->headers->clearCookie('authToken');
        return $response;
    }


    /**
     * @Route("/register/{code}", name="registration")
     * @param $code
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param ConfigurationManager $cm
     * @param AssociateManager $associateManager
     * @param LoggerInterface $databaseLogger
     * @param RecaptchaManager $recaptchaManager
     * @param string $siteKey
     * @param string $secretKey
     * @return Response
     */
    public function registration(
        $code,
        Request $request,
        InvitationManager $invitationManager,
        ConfigurationManager $cm,
        AssociateManager $associateManager,
        LoggerInterface $databaseLogger,
        RecaptchaManager $recaptchaManager,
        string $siteKey,
        string $secretKey
    ) {
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        if ($configuration->hasPrelaunchEnded()) {
            return $this->redirectToRoute('landingpage');
        }

        $invitation = $invitationManager->findInvitation($code);
        $parentAssociate = $associateManager->findByUserName($code);

        if ($parentAssociate) {
            return $this->redirectToRoute('invite', ['id' => $parentAssociate->getId()]);
        }

        if (!$invitation) {
            return $this->render('home/linkstate.html.twig');
        }

        $termsOfServices = $configuration->getTermsOfServices();

        $disclaimer = $configuration->getTosDisclaimer();

        $user = new User();
        $associate = new Associate();
        $associate->setEmail($invitation->getEmail());
        $user->setEmail($invitation->getEmail());
        $associate->setFullName($invitation->getFullName());
        $user->setAssociate($associate);

        $form = $this->createForm(UserRegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $invitation->getEmail();
            $user->setEmail($email);
            $checkEmailExist = $em->getRepository(User::class)->findBy(['email' => $email]);

            $recaptchaResponse = $request->request->get('g-recaptcha-response');
            $recaptchaError = $recaptchaManager->validateRecaptcha($recaptchaResponse, $secretKey);
            $env = $this->getParameter('kernel.environment');

            if ($recaptchaError && $env !== 'test') {
                $this->addFlash('error', $recaptchaError);
            } elseif ($checkEmailExist) {
                $this->addFlash('error', 'This email already exist');
            } else {
                $associate->setParent($invitation->getSender());
                $invitationManager->discardInvitation($invitation);
                $associate->setEmail(trim($email));
                $associate->setFullName(trim($associate->getFullName()));
                $invitationUserName = $associateManager->createUniqueUserNameInvitation($associate->getFullName());
                $associate->setInvitationUserName($invitationUserName);

                $user->setRoles(['ROLE_USER']);
                $user->setAssociate($associate);
                $invitationManager->sendWelcomeEmail($associate);
                $user->getAssociate()->setMobilePhone(
                    '+' . $form['associate']['mobilePhone']->getData()->getCountryCode() .
                    $form['associate']['mobilePhone']->getData()->getNationalNumber()
                );
                $em->persist($associate);
                $em->persist($user);

                $em->flush();
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->container->get('security.token_storage')->setToken($token);
                $this->container->get('session')->set('_security_main', serialize($token));

                $databaseLogger->info($associate->getFullName(). ' registered');

                return $this->redirect('http://localhost:8080');
            }
        }

        $recruiter = $associateManager->getAssociate($invitation->getSender()->getId(), true);

        return $this->render('home/registration.html.twig', [
            'registration' => $form->createView(),
            'termsOfServices' => $termsOfServices,
            'recruiter' => $recruiter,
            'disclaimer' => $disclaimer,
            'email' => $invitation->getEmail(),
            'siteKey' => $siteKey
        ]);
    }

    private function recaptchaResponse($recaptchaResponse, $secretKey)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .
            '&response=' . urlencode($recaptchaResponse);
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    /**
     * @Route("/invite/{id}", name="invite")
     * @param $id
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param BlacklistManager $blacklistManager
     * @param LoggerInterface $databaseLogger
     * @param RecaptchaManager $recaptchaManager
     * @param string $siteKey
     * @param string $secretKey
     * @return Response
     */
    public function invitation(
        $id,
        Request $request,
        InvitationManager $invitationManager,
        BlacklistManager $blacklistManager,
        LoggerInterface $databaseLogger,
        RecaptchaManager $recaptchaManager,
        string $siteKey,
        string $secretKey
    ) {
        $em = $this->getDoctrine()->getManager();

        $associate = $em->getRepository(Associate::class)->find($id);

        if (!$associate) {
            throw new NotFoundHttpException('Cannot find associate', null, 404);
        }

        $form = $this->createForm(InvitationType::class, null, ['label' => 'get invited']);
        $form->handleRequest($request);

        if (($form->isSubmitted() && $form->isValid())) {
            $em = $this->getDoctrine()->getManager();
            $invitation = new Invitation();
            $email = trim($form['email']->getData());
            /** @var AssociateRepository $associateRepo */
            $associateRepo = $em->getRepository(Associate::class);

            $recaptchaResponse = $request->request->get('g-recaptcha-response');
            $recaptchaError = $recaptchaManager->validateRecaptcha($recaptchaResponse, $secretKey);
            $env = $this->getParameter('kernel.environment');

            if ($recaptchaError && $env !== 'test') {
                $this->addFlash('error', $recaptchaError);
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Invalid email');
            } elseif ($associateRepo->findAssociatesFilterCount(((new AssociateFilter())->setEmail($email))) > 0) {
                $this->addFlash('error', 'Associate with this email already exists');
            } elseif ($blacklistManager->existsInBlacklist($email)) {
                $form
                    ->get('email')
                    ->addError(new FormError('The person with this email has opted out of this service'));
            } else {
                $fullName = $form['fullName']->getData();
                $invitation->setSender($associate);
                $invitation->setEmail($email);
                $invitation->setFullName($fullName);
                $invitationManager->send($invitation);
                $em->persist($invitation);
                $em->flush();

                $databaseLogger->info(
                    $associate->getFullName().
                    ' sent invitation to '. $fullName. ' (' .$email.')'
                );

//                $this->addFlash('success', 'Email sent');
                return $this->render('home/invitation.html.twig', [
                    'invitation' => $form->createView(),
                    'id' => $id,
                    'sent' => [
                        'completed' => true,
                        'address' => $email,
                    ],
                ]);
            }
        }

        return $this->render('home/invitation.html.twig', [
            'invitation' => $form->createView(),
            'associateFullName' => $associate->getFullName(),
            'siteKey' => $siteKey
        ]);
    }

    /**
     * @Route("/landingpage", name="landingpage")
     * @param ConfigurationManager $cm
     * @return RedirectResponse|Response
     */
    public function landingPage(ConfigurationManager $cm)
    {
        $configuration = $cm->getConfiguration();

        if (!$configuration->hasPrelaunchEnded()) {
            return $this->redirectToRoute('home');
        }

        $landingContent = $configuration->getLandingContent();

        $landingContent = $cm->getParsedLandingContent($landingContent);

        return $this->render('home/landingPage.html.twig', [
            'landingContent' => $landingContent
        ]);
    }

    /**
     * @Route("/optOut/{invitationCode}", name="opt_out_email")
     * @param $invitationCode
     * @param BlacklistManager $blacklistManager
     * @return Response
     */
    public function optOutAction($invitationCode, BlacklistManager $blacklistManager)
    {
        $message = 'You have already opted out of the service';
        if (!$blacklistManager->existsInBlacklistByCode($invitationCode)) {
            $blacklistManager->addToBlacklist($invitationCode);
            $message = 'Successfully opted out of the service';
        }
        return $this->render('home/optOut.html.twig', ['message' => $message]);
    }

    /**
     * @Route("/restorePassword", name="forgot_password")
     * @param Request $request
     * @param ResetPasswordManager $resetPasswordManager
     * @return Response
     */
    public function forgotPassword(Request $request, ResetPasswordManager $resetPasswordManager)
    {
        $success = false;
        $email = "";
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = trim($form['email']->getData());
            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!$user) {
                $this->addFlash('error', 'This email doesnt exist');
            } else {
                $resetPasswordManager->resetPassword($user);
                $success = true;
                $email = $user->getEmail();
            }
        }
        return $this->render('home/forgotPassword.html.twig', [
            'form' => $form->createView(), 'success' => $success, 'email' => $email
        ]);
    }

    /**
     * @Route("/restorePassword/{code}", name="restore_password")
     * @param $code
     * @param Request $request
     * @param ResetPasswordManager $resetPasswordManager
     * @param LoggerInterface $databaseLogger
     * @return Response
     */
    public function restorePassword(
        $code,
        Request $request,
        ResetPasswordManager $resetPasswordManager,
        LoggerInterface $databaseLogger
    ) {
        $em = $this->getDoctrine()->getManager();
        $user = $resetPasswordManager->findUser($code);

        if (!$user) {
            return $this->render(
                'home/linkstate.html.twig',
                ['wrongRestorePassword' => 'There is no user with this code or its already expired']
            );
        }

        $form = $this->createForm(NewPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            $newPassword = $form['newPassword']->getData();

            if (!$newPassword) {
                $this->addFlash('error', 'Passsword cannot be empty');
            } else {
                $user->setPlainPassword($newPassword);
                $em->persist($user);
                $em->flush();
                $resetPasswordManager->discardCode($user);
                $this->addFlash('success', 'Password has been restored!');

                $databaseLogger->info('Password has been restored for '.$user->getEmail(). 'email, ');
                return $this->redirectToRoute('login');
            }
        }
        return $this->render('home/newPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logo", name="main_logo")
     * @param ConfigurationManager $cm
     * @param Packages $packages
     * @return Response
     */
    public function mainLogo(ConfigurationManager $cm, Packages $packages)
    {
        $configuration = $cm->getConfiguration();

        $path = $this->getParameter('kernel.project_dir').'/public/assets/images/plum_tree_logo.png';

        if ($configuration->getMainLogo()) {
            return $this->forward(
                'PlumTreeSystemsFileBundle:File:download',
                ['id' => $configuration->getMainLogo()->getId()]
            );
        }

        $file = file_get_contents($path);

        $headers = [
            'Content-Type'     => 'image/png',
            'Content-Disposition' => 'inline; filename="plum_tree_logo.png"'
        ];
        return new Response($file, 200, $headers);
    }
}
