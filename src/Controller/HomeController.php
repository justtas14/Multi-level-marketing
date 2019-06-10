<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Configuration;
use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Service\ConfigurationManager;
use App\Service\InvitationManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $termsOfServices = $configuration->getTermsOfServices();

        $user = new User();
        $associate = new Associate();
        $user->setEmail($invitation->getEmail());
        $associate->setFullName($invitation->getFullName());
        $user->setAssociate($associate);

        $form = $this->createForm(UserRegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail();
            $checkEmailExist = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($checkEmailExist) {
                $this->addFlash('error', 'This email already exist');
            } else {
                $associate->setParent($invitation->getSender());
                $associate->setEmail($email);

                $user->setRoles(['ROLE_USER']);
                $user->setAssociate($associate);
                $invitationManager->discardInvitation($invitation);

                $em->persist($associate);
                $em->flush();
                $em->persist($user);
                $em->flush();

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

        if (!$configuration->hasPrelaunchEnded()) {
            return $this->redirectToRoute('home');
        }

        $landingContent = $configuration->getLandingContent();

        return $this->render('home/landingPage.html.twig', [
            'landingContent' => $landingContent
        ]);
    }
}
