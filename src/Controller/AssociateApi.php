<?php

namespace App\Controller;

use App\CustomNormalizer\AssociateNormalizer;
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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api", name="api_")
 */

final class AssociateApi extends AbstractController
{
    const INVITATION_LIMIT = 10;

    /**
     * @Route("/home-associate", name="associate")
     * @param AssociateManager $associateManager
     * @param AssociateNormalizer $associateNormalizer
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function index(AssociateManager $associateManager, AssociateNormalizer $associateNormalizer)
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

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), $associateNormalizer, new JsonEncoder()]
        );

        $serializedDirectAssociates = $serializer->normalize(
            $directAssociates,
            null,
            ['attributes' => ['id', 'email', 'fullName', 'mobilePhone', 'joinDate']]
        );

        $userParent = $user->getAssociate()->getParent();
        if ($userParent->getId() == -1) {
            $serializedParent = null;
        } else {
            $serializedParent = $serializer->normalize(
                $userParent,
                null,
                ['attributes' => ['id', 'email', 'fullName', 'mobilePhone']]
            );
        }

        $data = [
            'associatesInLevels' => $associateInLevels,
            'maxLevel' => $level,
            'parent' => $serializedParent,
            'directAssociates' => $serializedDirectAssociates
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/associate/profile", name="associate_profile")
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @param GaufretteFileManager $fileManager
     * @param LoggerInterface $databaseLogger
     * @param AssociateNormalizer $associateNormalizer
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function associateProfile(
        UserPasswordEncoderInterface $encoder,
        Request $request,
        GaufretteFileManager $fileManager,
        LoggerInterface $databaseLogger,
        AssociateNormalizer $associateNormalizer
    ) {
        $formErrors = [];
        $updated = false;

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
                    $formErrors = ['invalidEmail' => 'This email already exists'];
                } elseif (!$encoder->isPasswordValid($user, $plainPassword)) {
                    $formErrors = ['invalidPassword' => 'Old password is not correct'];
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

                    $updated = true;
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
                $formErrors = ['phoneError' => 'Prior mobile is invalid'];
            }
        }

        $em->refresh($user);
        $em->refresh($user->getAssociate());


        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), $associateNormalizer, new JsonEncoder()]
        );

        $serializedAssociate = $serializer->normalize(
            $user->getAssociate(),
            null,
            ['attributes' => [
                'id',
                'fullName',
                'email',
                'address',
                'address2',
                'city',
                'postcode',
                'country',
                'mobilePhone',
                'homePhone',
                'profilePicture',
                'agreedToEmailUpdates',
                'agreedToTextMessageUpdates',
                'agreedToTermsOfService',

            ]]
        );

        $data = [
            'associate' => $serializedAssociate
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/associate/invite", name="associate_invite")
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param BlacklistManager $blacklistManager
     * @param LoggerInterface $databaseLogger
     * @param AssociateNormalizer $associateNormalizer
     * @param string $siteKey
     * @param string $secretKey
     * @return Response
     * @throws NotInvitationSender
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function associateInvitation(
        Request $request,
        InvitationManager $invitationManager,
        BlacklistManager $blacklistManager,
        LoggerInterface $databaseLogger,
        AssociateNormalizer $associateNormalizer,
        string $siteKey,
        string $secretKey
    ) {
        $formErrors = [];

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
                $formErrors = ['recaptchaError' => 'Please check the captcha form'];
            } elseif (!$responseKeys["success"] && $env !== 'test') {
                $formErrors = ['recaptchaError' => 'You are the spammer!'];
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $formErrors = ['invalidEmail' => 'Invalid email'];
            } elseif ($associateRepo->findAssociatesFilterCount(((new AssociateFilter())->setEmail($email))) > 0) {
                $formErrors = ['invalidEmail' => 'Associate already exists'];
            } elseif ($blacklistManager->existsInBlacklist($email)) {
                $formErrors = ['invalidEmail' => 'The person with this email has opted out of this service'];
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
                $data = [
                    'sent' => [
                        'completed' => true,
                        'address' => $email
                    ]
                ];

                return new JsonResponse($data);
            }
        }
        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), new ObjectNormalizer(), new JsonEncoder()]
        );

        $serializedInvitations = $serializer->normalize(
            $invitations,
            null,
            ['attributes' => ['id', 'email', 'fullName', 'sender', 'used', 'created']]
        );

        $data = [
            'formErrors' => $formErrors,
            'invitations' => $serializedInvitations,
            'pagination' => [
                'numberOfPages' => $numberOfPages,
                'currentPage' => $page
            ],
            'uniqueAssociateInvitationLink' => $uniqueAssociateInvitationLink,
            'siteKey' => $siteKey
        ];

        return new JsonResponse($data);
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
}
