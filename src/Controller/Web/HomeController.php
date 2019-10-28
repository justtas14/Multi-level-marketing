<?php

namespace App\Controller\Web;

use App\Entity\Associate;
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
use App\Service\ResetPasswordManager;
use PlumTreeSystems\UserBundle\Service\JWTManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"}, name="index")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('main.html.twig', []);
    }

    /**
     * @Route("/api/authenticate", name="authentication")
     * @param Request $request
     * @param JWTManager $JWTManager
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function authenticate(Request $request, JWTManager $JWTManager): Response
    {
        $isAuthenticated = true;
        $serializedUser = null;
        $em = $this->getDoctrine()->getManager();

        $tokenData = $request->request->all();
        $payload = $JWTManager->getPayload($tokenData['token']);

        if (!$tokenData || !$payload) {
            $isAuthenticated = false;
        } else {
            $email = $payload;

            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            $serializer = new Serializer([
                new DateTimeNormalizer('Y-m-d h:i:s'),
                new ObjectNormalizer(), new JsonEncoder()
            ]);

            $data = $serializer->normalize(
                $user,
                null,
                ['attributes' =>
                    [
                        'id',
                        'associate',
                        'roles',


                    ]
                ]
            );
        }

        $payload = [
            'user' => $serializedUser,
            'isAuthenticated' => $isAuthenticated
        ];

        return new JsonResponse($payload, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $user = $this->getUser();
        /**
         * @var User $user
         */
        if (!$user) {
            return $this->redirectToRoute('login');
        } elseif (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('admin');
        } else {
            return $this->redirectToRoute('associate');
        }
    }

    /**
     * @Route("/register/{code}", name="registration")
     * @param $code
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param ConfigurationManager $cm
     * @param AssociateManager $associateManager
     * @param LoggerInterface $databaseLogger
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
        string $siteKey,
        string $secretKey
    ) {
        $em = $this->getDoctrine()->getManager();
        $invitation = $invitationManager->findInvitation($code);
        $parentAssociate = $associateManager->findByUserName($code);

        if ($parentAssociate) {
            return $this->redirectToRoute('invite', ['id' => $parentAssociate->getId()]);
        }

        if (!$invitation) {
            return $this->render('home/linkstate.html.twig');
        }

        $configuration = $cm->getConfiguration();
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
            $env = $this->getParameter('kernel.environment');

            if (!$recaptchaResponse && $env !== 'test') {
                $this->addFlash('error', 'Please check the captcha form');
            } elseif (!$this->recaptchaResponse($recaptchaResponse, $secretKey)["success"] && $env !== 'test') {
                $this->addFlash('error', 'You are the spammer!');
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

                return $this->redirectToRoute('home');
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
        $responseKeys = json_decode($response, true);
        return $responseKeys;
    }

    /**
     * @Route("/invite/{id}", name="invite")
     * @param $id
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param BlacklistManager $blacklistManager
     * @param LoggerInterface $databaseLogger
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
        string $siteKey,
        string $secretKey
    ) {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(Associate::class)->find($id);

        if (!$user) {
            throw new NotFoundHttpException('Cannot find associate', null, 404);
        }

        $associate = $user->getAssociate();

        $form = $this->createForm(InvitationType::class, null, ['label' => 'get invited']);
        $form->handleRequest($request);

        if (($form->isSubmitted() && $form->isValid())) {
            $em = $this->getDoctrine()->getManager();
            $invitation = new Invitation();
            $email = trim($form['email']->getData());
            /** @var AssociateRepository $associateRepo */
            $associateRepo = $em->getRepository(Associate::class);

            $recaptchaResponse = $request->request->get('g-recaptcha-response');
            $env = $this->getParameter('kernel.environment');

            if (!$recaptchaResponse && $env !== 'test') {
                $this->addFlash('error', 'Please check the captcha form');
            } elseif (!$this->recaptchaResponse($recaptchaResponse, $secretKey)["success"] && $env !== 'test') {
                $this->addFlash('error', 'You are the spammer!');
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
        $em = $this->getDoctrine()->getManager();

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
                return $this->redirectToRoute('home');
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
