<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Configuration;
use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Service\BlacklistManager;
use App\Service\ConfigurationManager;
use App\Service\InvitationManager;
use DateTime;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
     * @return Response
     * @throws \Exception
     */
    public function registration(
        $code,
        Request $request,
        InvitationManager $invitationManager,
        ConfigurationManager $cm
    ) {
        $em = $this->getDoctrine()->getManager();
        $invitation = $invitationManager->findInvitation($code);

        if (!$invitation) {
            return $this->render('home/linkstate.html.twig');
        }

        $configuration = $cm->getConfiguration();
        $termsOfServices = null;
        if ($configuration && $configuration->getTermsOfServices()) {
            $termsOfServices = $configuration->getTermsOfServices();
        }

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
            $checkEmailExist = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            $dob = $form->get('associate')->get('dateOfBirth');
            if ($checkEmailExist) {
                $this->addFlash('error', 'This email already exist');
            } elseif (!$dob->getData() || (is_string($dob->getData()) && $dob->getData() === '')) {
                $dob->addError(new FormError('Date of birth cannot be empty'));
            } else {
                $dob = $form->get('associate')->get('dateOfBirth')->getData();
                if ($dob && is_string($dob) && $dob !== '') {
                    $user->getAssociate()->setDateOfBirth(new DateTime($dob));
                }
                $associate->setParent($invitation->getSender());
                $associate->setEmail($email);

                $user->setRoles(['ROLE_USER']);
                $user->setAssociate($associate);
                $invitationManager->discardInvitation($invitation);

                $em->persist($associate);
                $em->flush();
                $em->persist($user);
                $em->flush();
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->container->get('security.token_storage')->setToken($token);
                $this->container->get('session')->set('_security_main', serialize($token));

                $this->addFlash('success', 'Registration completed successfully');
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('home/registration.html.twig', [
            'registration' => $form->createView(),
            'termsOfServices' => $termsOfServices
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

        if (!$configuration || !$configuration->hasPrelaunchEnded()) {
            return $this->redirectToRoute('home');
        }

        $landingContent = $configuration->getLandingContent();

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
        if (!$invitationCode) {
            $message = 'No code';
        } elseif (!$blacklistManager->existsInBlacklistByCode($invitationCode)) {
            $blacklistManager->addToBlacklist($invitationCode);
            $message = 'Successfully opted out of the service';
        }
        return $this->render('home/optOut.html.twig', ['message' => $message]);
    }
}
