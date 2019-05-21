<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\User;
use App\Form\UserType;
use App\Service\InvitationManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/register/{code}", name="registration")
     * @param $code
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registration($code, Request $request, InvitationManager $invitationManager)
    {
        $em = $this->getDoctrine()->getManager();
        $invitation = $invitationManager->findInvitation($code);

        if (!$invitation) {
            return $this->render('home/linkstate.html.twig');
        }

        $user = new User();
        $associate = new Associate();
        $user->setAssociate($associate);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail();
            $checkEmailExist = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($checkEmailExist) {
                $this->addFlash('error', 'This email already exist');
            } else {
                $associate->setParent($invitation->getSender());
                $associate->setEmail($email);

                $user->setPlainPassword($form['newPassword']->getData());
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
            'email' => $invitation->getEmail(),
            'fullName' => $invitation->getFullName()
        ]);
    }
}
