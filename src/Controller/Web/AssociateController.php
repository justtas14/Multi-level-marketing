<?php

namespace App\Controller\Web;

use App\Entity\Associate;
use App\Entity\Invitation;
use App\Entity\User;
use App\Exception\NotAncestorException;
use App\Exception\NotInvitationSender;
use App\Exception\WrongPageNumberException;
use App\Filter\AssociateFilter;
use App\Form\InvitationType;
use App\Form\UserUpdateType;
use App\Repository\AssociateRepository;
use App\Service\AssociateManager;
use App\Service\BlacklistManager;
use App\Service\InvitationManager;
use Exception;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class AssociateController extends AbstractController
{
    const INVITATION_LIMIT = 10;

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
     * @param LoggerInterface $databaseLogger
     * @param string $siteKey
     * @param string $secretKey
     * @return Response
     * @throws NotInvitationSender
     */
    public function associateInvitation(
        Request $request,
        InvitationManager $invitationManager,
        BlacklistManager $blacklistManager,
        LoggerInterface $databaseLogger,
        string $siteKey,
        string $secretKey
    ) {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $associate = $user->getAssociate();

        $uniqueAssociateUsername= $associate->getInvitationUserName();

        $uniqueAssociateInvitationLink = $invitationManager->getAssociateUrl($uniqueAssociateUsername);

        $page = $request->get('page', 1);

        if (!is_numeric($page)) {
            throw new WrongPageNumberException();
        }

        $invitationId = $request->get('invitationId');

        $invitationRepository = $em->getRepository(Invitation::class);

        if ($invitationId) {
            $checkInvitation = $invitationRepository->findOneBy(
                ['sender' => $user, 'id' => $invitationId]
            );
            if (!$checkInvitation) {
                throw new NotInvitationSender('Invitation with id of '. $invitationId. ' cannot be reached', 500);
            }
        }

        $allInvitations = $invitationRepository->findBy(['sender' => $associate]);

        $numberOfPages = ceil(count($allInvitations) / self::INVITATION_LIMIT);

        if ($numberOfPages == 0) {
            $numberOfPages++;
        }

        if (($page < 1 || $page > $numberOfPages)) {
            throw new WrongPageNumberException('Page ' . $page . ' doesnt exist');
        }

        $invitations = $invitationRepository->findBy(
            ['sender' => $associate],
            ['created' => 'DESC'],
            self::INVITATION_LIMIT,
            self::INVITATION_LIMIT * ($page-1)
        );

        $form = $this->createForm(InvitationType::class, null, ['label' => 'send']);
        $form->handleRequest($request);

        if (($form->isSubmitted() && $form->isValid()) || $invitationId) {
            $em = $this->getDoctrine()->getManager();
            if (!$invitationId) {
                $invitation = new Invitation();
                $email = trim($form['email']->getData());
            } else {
                /** @var Invitation $resendInvitation */
                $resendInvitation = $em->getRepository(Invitation::class)->find($invitationId);
                $email = $resendInvitation->getEmail();
            }
            /** @var AssociateRepository $associateRepo */
            $associateRepo = $em->getRepository(Associate::class);
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .
                '&response=' . urlencode($recaptchaResponse);
            $response = file_get_contents($url);
            $responseKeys = json_decode($response, true);

            $env = $this->getParameter('kernel.environment');

            if (!$recaptchaResponse && $env !== 'test') {
                $this->addFlash('error', 'Please check the captcha form');
            } elseif (!$responseKeys["success"] && $env !== 'test') {
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
                if (!$invitationId) {
                    $invitation->setSender($associate);
                    $invitation->setEmail($email);
                    $invitation->setFullName($form['fullName']->getData());
                    $invitationManager->send($invitation);
                    $em->persist($invitation);
                    $em->flush();
                    $databaseLogger->info(
                        $associate->getFullName().
                        ' sent invitation to '. $invitation->getFullName(). ' (' .$invitation->getEmail().')'
                    );
                } else {
                    $invitationManager->send($resendInvitation);
                    $em->persist($resendInvitation);
                    $em->flush();
                    $databaseLogger->info(
                        $associate->getFullName().
                        ' resent invitation to '. $resendInvitation->getFullName().
                        ' (' .$resendInvitation->getEmail().')'
                    );
                }
//                $this->addFlash('success', 'Email sent');
                return $this->render('associate/invitation.html.twig', [
                    'invitation' => $form->createView(),
                    'sent' => [
                        'completed' => true,
                        'address' => $email
                    ],
                ]);
            }
        }

        return $this->render('associate/invitation.html.twig', [
            'invitation' => $form->createView(),
            'invitations' => $invitations,
            'numberOfPages' => $numberOfPages,
            'currentPage' => $page,
            'uniqueAssociateInvitationLink' => $uniqueAssociateInvitationLink,
            'siteKey' => $siteKey
        ]);
    }

    /**
     * @Route("/associate/profile", name="associate_profile")
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @param GaufretteFileManager $fileManager
     * @param LoggerInterface $databaseLogger
     * @return Response
     * @throws Exception
     */
    public function associateProfile(
        UserPasswordEncoderInterface $encoder,
        Request $request,
        GaufretteFileManager $fileManager,
        LoggerInterface $databaseLogger
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
                    $associate = $user->getAssociate();
                    $associate->setMobilePhone(
                        '+' . $form['associate']['mobilePhone']->getData()->getCountryCode() .
                        $form['associate']['mobilePhone']->getData()->getNationalNumber()
                    );
                    $associate->setEmail($email);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->persist($associate);
                    $em->flush();
                    $databaseLogger->info($associate->getFullName(). ' profile updated');

                    $this->addFlash('success', 'Fields updated');
                }
            }
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $phoneNumber = $phoneUtil->parse($user->getAssociate()->getMobilePhone(), 'GB');
                $form->get('associate')->get('mobilePhone')->get('number')->setData($phoneNumber->getNationalNumber());
                $form->get('associate')->get('mobilePhone')->get('country')
                    ->setData($phoneUtil->getRegionCodeForCountryCode($phoneNumber->getCountryCode()));
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
     * @return Response
     */
    public function teamViewer()
    {
        return $this->render('associate/teamViewer.html.twig');
    }
}
