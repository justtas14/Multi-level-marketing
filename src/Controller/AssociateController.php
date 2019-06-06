<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\User;
use App\Form\InvitationType;
use App\Form\UserUpdateType;
use App\Service\AssociateManager;
use App\Service\InvitationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AssociateController extends AbstractController
{
    /**
     * @Route("/associate", name="associate")
     */
    public function index(AssociateManager $associateManager)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $level = $associateManager->getNumberOfLevels($user->getAssociate()->getAssociateId());

        $associateInLevels = [];

        for ($i = 1; $i <= $level; $i++) {
            $associateInLevels[$i] = $associateManager->getNumberOfAssociatesInDownline(
                $i,
                $user->getAssociate()->getAssociateId()
            );
        }

        $userParent = $user->getAssociate()->getParent();
        if ($userParent->getId() == -1) {
            $userParent = null;
        }

        return $this->render('associate/index.html.twig', [
            'associatesInLevels' => $associateInLevels,
            'parent' => $userParent
        ]);
    }

    /**
     * @Route("/associate/invite", name="associate_invite")
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function associateInvitation(Request $request, InvitationManager $invitationManager)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(InvitationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invitation = new Invitation();
            $email = trim($form['email']->getData());
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Invalid email');
            } else {
                $invitation->setSender($user->getAssociate());
                $invitation->setEmail($email);
                $invitation->setFullName($form['fullName']->getData());

                $invitationManager->send($invitation);

                $em = $this->getDoctrine()->getManager();
                $em->persist($invitation);
                $em->flush();

                $this->addFlash('success', 'Email sent');
            }
        }

        return $this->render('associate/invitation.html.twig', [
            'invitation' => $form->createView()
        ]);
    }

    /**
     * @Route("/associate/profile", name="associate_profile")
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function associateProfile(UserPasswordEncoderInterface $encoder, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $currentEmail = $user->getEmail();

        $form = $this->createForm(UserUpdateType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form['oldPassword']->getData();
            $email = $user->getEmail();
            $checkEmailExist = $em->getRepository(User::class)->findBy(['email' => $email]);

            if ($checkEmailExist && $currentEmail !== $email) {
                $this->addFlash('error', 'This email already exist');
            } elseif (!$encoder->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('error', 'Old password is not correct');
            } else {
                $newPassword = $form['newPassword']->getData();
                if ($newPassword) {
                    $user->setPlainPassword($newPassword);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->persist($user->getAssociate());
                $em->flush();

                $this->addFlash('success', 'Fields updated');
            }
        }

        $em->refresh($user);
        return $this->render('profile.html.twig', [
            'updateProfile' => $form->createView()
        ]);
    }
}
