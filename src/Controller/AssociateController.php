<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\File;
use App\Entity\Invitation;
use App\Entity\InvitationBlacklist;
use App\Entity\User;
use App\Exception\NotAncestorException;
use App\Filter\AssociateFilter;
use App\Form\InvitationType;
use App\Form\UserUpdateType;
use App\Repository\AssociateRepository;
use App\Service\AssociateManager;
use App\Service\BlacklistManager;
use App\Service\InvitationManager;
use DateTime;
use Exception;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AssociateController extends AbstractController
{
    /**
     * @Route("/associate", name="associate")
     * @param AssociateManager $associateManager
     * @return Response
     * @throws Exception
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

        $directAssociates = $associateManager->getAllDirectAssociates($user->getAssociate()->getAssociateId());

        $userParent = $user->getAssociate()->getParent();
        if ($userParent->getId() == -1) {
            $userParent = null;
        }

        return $this->render('associate/index.html.twig', [
            'associatesInLevels' => $associateInLevels,
            'parent' => $userParent,
            'directAssociates' => $directAssociates,
            'maxLevel' => $level
        ]);
    }

    /**
     * @Route("/associate/info", name="get_associate_broken")
     */
    public function getBrokenAssociate()
    {
        return new Response("");
    }

    /**
     * @Route("/associate/info/{id}", name="get_associate")
     * @param $id
     * @param AssociateManager $associateManager
     * @return Response
     * @throws NotAncestorException
     */
    public function getAssociate($id, AssociateManager $associateManager)
    {
        $associate = $associateManager->getAssociate($id);
        if (!$associate) {
            return $this->render('admin/associateInfo.html.twig', ['isCompany' => true]);
        }
        return $this->render('admin/associateInfo.html.twig', ['associate' => $associate]);
    }

    /**
     * @Route("/associate/invite", name="associate_invite")
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param BlacklistManager $blacklistManager
     * @return Response
     */
    public function associateInvitation(
        Request $request,
        InvitationManager $invitationManager,
        BlacklistManager $blacklistManager
    ) {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(InvitationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $invitation = new Invitation();
            $email = trim($form['email']->getData());
            /** @var AssociateRepository $associateRepo */
            $associateRepo = $em->getRepository(Associate::class);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Invalid email');
            } elseif ($associateRepo->findAssociatesFilterCount(((new AssociateFilter())->setEmail($email))) > 0) {
                $this->addFlash('error', 'Associate already exists');
            } elseif ($blacklistManager->existsInBlacklist($email)) {
                $form
                    ->get('email')
                    ->addError(new FormError('The person with this email has opted out of this service'));
            } else {
                $invitation->setSender($user->getAssociate());
                $invitation->setEmail($email);
                $invitation->setFullName($form['fullName']->getData());

                $invitationManager->send($invitation);

                $em->persist($invitation);
                $em->flush();
//                $this->addFlash('success', 'Email sent');
                return $this->render('associate/invitation.html.twig', [
                    'invitation' => $form->createView(),
                    'sent' => [
                        'completed' => true,
                        'address' => $invitation->getEmail()
                    ]
                ]);
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
     * @return Response
     * @throws Exception
     */
    public function associateProfile(
        UserPasswordEncoderInterface $encoder,
        Request $request,
        GaufretteFileManager $fileManager
    ) {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $currentEmail = $user->getEmail();

        $savedProfilePicture = null;
        if ($user->getAssociate()->getProfilePicture() !== null) {
            $savedProfilePicture = $user->getAssociate()->getProfilePicture();
            $user->getAssociate()->setProfilePicture(null);
        }

        $form = $this->createForm(UserUpdateType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $plainPassword = $form['oldPassword']->getData();
                $email = $user->getEmail();
                $checkEmailExist = $em->getRepository(User::class)->findBy(['email' => $email]);
                if ($checkEmailExist && $currentEmail !== $email) {
                    $this->addFlash('error', 'This email already exist');
                } elseif (!$encoder->isPasswordValid($user, $plainPassword)) {
                    $this->addFlash('error', 'Old password is not correct');
                } else {
                    if ($savedProfilePicture) {
                        if ($user->getAssociate()->getProfilePicture() === null) {
                            $user->getAssociate()->setProfilePicture($savedProfilePicture);
                        } else {
                            $fileManager->removeEntity($savedProfilePicture);
                        }
                    }
                    $newPassword = $form['newPassword']->getData();
                    if ($newPassword) {
                        $user->setPlainPassword($newPassword);
                    }
                    $user->getAssociate()->setMobilePhone(
                        '+' . $form['associate']['mobilePhone']->getData()->getCountryCode() .
                        $form['associate']['mobilePhone']->getData()->getNationalNumber()
                    );
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->persist($user->getAssociate());
                    $em->flush();

                    $this->addFlash('success', 'Fields updated');
                }
            }
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $phoneNumber = $phoneUtil->parse($user->getAssociate()->getMobilePhone(), 'GB');
//                $phoneNumber->clearCountryCode();
                $form->get('associate')->get('mobilePhone')->get('number')->setData($phoneNumber->getNationalNumber());
                $form->get('associate')->get('mobilePhone')->get('country')->setData($phoneNumber->getCountryCode());
            } catch (NumberParseException $e) {
                $form->get('associate')->get('mobilePhone')->addError(new FormError('Prior mobile is invalid'));
            }
        }

        $em->refresh($user);
        $em->refresh($user->getAssociate());
        return $this->render('profile.html.twig', [
            'updateProfile' => $form->createView()
        ]);
    }

    /**
     * @Route("/associate/downline", name="direct_downline")
     * @param Request $request
     * @param AssociateManager $associateManager
     * @return JsonResponse
     */
    public function directDownline(Request $request, AssociateManager $associateManager)
    {
        $id = $request->get('id');
        return new JsonResponse($associateManager->getDirectDownlineAssociates($id), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/associate/viewer", name="team_viewer")
     * @param Request $request
     * @return Response
     */
    public function teamViewer(Request $request)
    {
        return $this->render('associate/teamViewer.html.twig');
    }
}
