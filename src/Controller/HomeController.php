<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Gallery;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\EditorImageType;
use App\Form\NewPasswordType;
use App\Form\ResetPasswordType;
use App\Form\UserRegistrationType;
use App\CustomNormalizer\GalleryNormalizer;
use App\Service\AssociateManager;
use App\Service\BlacklistManager;
use App\Service\ConfigurationManager;
use App\Service\InvitationManager;
use App\Service\ResetPasswordManager;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class HomeController extends AbstractController
{
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

        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/register/{code}", name="registration")
     * @param $code
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param ConfigurationManager $cm
     * @param AssociateManager $associateManager
     * @return Response
     * @throws \Exception
     */
    public function registration(
        $code,
        Request $request,
        InvitationManager $invitationManager,
        ConfigurationManager $cm,
        AssociateManager $associateManager
    ) {
        $em = $this->getDoctrine()->getManager();
        $invitation = $invitationManager->findInvitation($code);

        if (!$invitation) {
            return $this->render('home/linkstate.html.twig');
        }

        $configuration = $cm->getConfiguration();
        $termsOfServices = $configuration->getTermsOfServices();

        $disclaimer = $configuration->getTosDisclaimer();

        $user = new User();
        $associate = new Associate();
        $user->setEmail($invitation->getEmail());
        $associate->setEmail($invitation->getEmail());
        $associate->setFullName($invitation->getFullName());
        $user->setAssociate($associate);

        $form = $this->createForm(UserRegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail();
            $associate->setParent($invitation->getSender());
            $associate->setEmail($email);

            $user->setRoles(['ROLE_USER']);
            $user->setAssociate($associate);
            $invitationManager->discardInvitation($invitation);
            $invitationManager->sendWelcomeEmail($associate);
            $user->getAssociate()->setMobilePhone(
                '+' . $form['associate']['mobilePhone']->getData()->getCountryCode() .
                $form['associate']['mobilePhone']->getData()->getNationalNumber()
            );
            $em->persist($associate);
            $em->flush();
            $em->persist($user);
            $em->flush();
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));
            return $this->redirectToRoute('home');
        }

        $recruiter = $associateManager->getAssociate($invitation->getSender(), true);
        return $this->render('home/registration.html.twig', [
            'registration' => $form->createView(),
            'termsOfServices' => $termsOfServices,
            'recruiter' => $recruiter,
            'disclaimer' => $disclaimer
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

        try {
            $parser = new \DBlackborough\Quill\Parser\Html();
            $renderer = new \DBlackborough\Quill\Renderer\Html();

            $parser->load($landingContent)->parse();

            $landingContent = $renderer->load($parser->deltas())->render();
        } catch (\Exception $exception) {
            $landingContent = $configuration->getLandingContent();
        }

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
     * @param Request $request
     * @param ResetPasswordManager $resetPasswordManager
     * @return Response
     */
    public function restorePassword($code, Request $request, ResetPasswordManager $resetPasswordManager)
    {
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

    /**
     * @Route("/uploadFile", name="upload_file")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function uploadEditorImage(
        Request $request,
        GaufretteFileManager $gaufretteFileManager
    ) {
        if ($request->isMethod('POST')) {
            $uploadedFile = $request->getContent();

            $fileId = json_decode($uploadedFile);

            $em = $this->getDoctrine()->getManager();

            $galleryRepository = $em->getRepository(Gallery::class);

            /** @var Gallery $galleryFile */
            $galleryFile = $galleryRepository->find($fileId);

            $url = $gaufretteFileManager->generateDownloadUrl($galleryFile->getGalleryFile());

            $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            $absoluteUrl = $baseUrl.$url;

            return new JsonResponse($absoluteUrl, 200);
        } else {
            return new Response('', 200);
        }
    }

    /**
     * @Route("/uploadGalleryFile", name="upload_gallery_file")
     * @param Request $request
     * @param GalleryNormalizer $galleryNormalizer
     * @return JsonResponse|Response
     * @throws ExceptionInterface
     */
    public function uploadGalleryFile(
        Request $request,
        GalleryNormalizer $galleryNormalizer
    ) {
        if ($request->isMethod('POST')) {
            $galleryFile = new Gallery();

            $form = $this->createForm(EditorImageType::class, $galleryFile);
            $form->submit($request->files->all());

            $em = $this->getDoctrine()->getManager();

            $em->persist($galleryFile);
            $em->flush();

            $galleryFile->getGalleryFile()->setUploadedFileReference(null);
            $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $galleryNormalizer, new JsonEncoder()]);
            $serializedFile = $serializer->normalize(
                $galleryFile,
                null,
                ['attributes' => ['id', 'galleryFile', 'mimeType', 'created']]
            );

            return new JsonResponse(
                [
                    'file' => $serializedFile
                ],
                JsonResponse::HTTP_OK
            );
        } else {
            return new Response('', 200);
        }
    }
}
