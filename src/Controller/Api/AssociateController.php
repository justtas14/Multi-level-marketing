<?php

namespace App\Controller\Api;

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
use App\Service\RecaptchaManager;
use Exception;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use PlumTreeSystems\UserBundle\Service\JWTManager;
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
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @OA\Info(title="Associate API", version="0.1")
 * @Rest\Route("/api", name="_api")
 */


final class AssociateController extends AbstractController
{
    const INVITATION_LIMIT = 10;


    /**
     * @OA\Get(
     *     path="/api/associate",
     *     @OA\Response(response="200", description="Returns associate home page relevant information in json")
     * )
     */

    /**
     * @Rest\Get("/associate", name="associate")
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
        $maxLevel = 1;

        $level = $associateManager->getNumberOfLevels($user->getAssociate()->getAssociateId());

        $associateInLevels = [];

        for ($i = 1; $i <= $level; $i++) {
            $associateInLevels[$i] = $associateManager->getNumberOfAssociatesInDownline(
                $i,
                $user->getAssociate()->getAssociateId()
            );
        }
        if (sizeof($associateInLevels) > 0) {
            $maxLevel = max($associateInLevels);
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
            'levels' => $level,
            'maxLevel' => $maxLevel,
            'parent' => $serializedParent,
            'directAssociates' => $serializedDirectAssociates
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Post(
     *     path="/api/associate/profile",
     *     @OA\Parameter(
     *         in="formData",
     *         name="email",
     *         required=true,
     *         description="Associate email",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="oldPassword",
     *         required=true,
     *         description="Associate old password",
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *         in="formData",
     *         name="newPassword",
     *         required=false,
     *         description="Associate old password",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="first",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="second",
     *                 type="string"
     *             ),
     *             example={"first": "123456", "second": "123456"}
     *          )
     *      @OA\Parameter(
     *         in="formData",
     *         name="associate",
     *         required=false,
     *         description="Associate old password",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="fullName",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="address",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="address2",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="postcode",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="country",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="mobilePhone",
     *                 type="object"
     *             ),
     *             @OA\Property(
     *                 property="agreedToEmailUpdates",
     *                 type="boolean"
     *             ),
     *             @OA\Property(
     *                 property="agreedToTextMessageUpdates",
     *                 type="boolean"
     *             ),
     *             @OA\Property(
     *                 property="agreedToTermsOfService",
     *                 type="boolean"
     *             ),
     *         )
     *      ),
     *     @OA\Response(response="200",
     *     description="Returns information about current associate or updates current associate")
     * )
     */

    /**
     * @Rest\Post("/associate/profile", name="associate_profile")
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
        $country = 'GB';
        $number = '';

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

        $formData = $request->request->all();

        if ($formData) {
            $formData['associate']['agreedToEmailUpdates'] = $formData['associate']['agreedToEmailUpdates'] === 'true' ?
                true : false;
            $formData['associate']['agreedToTextMessageUpdates'] =
                $formData['associate']['agreedToTextMessageUpdates'] === 'true' ? true : false;
            $formData['associate']['agreedToTermsOfService'] =
                $formData['associate']['agreedToTermsOfService'] === 'true' ? true : false;
            if ($request->files->all()) {
                $formData['associate']['profilePicture'] = $request->files->all()['associate']['profilePicture'];
            }
            $form->submit($formData);
        }

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $plainPassword = $form['oldPassword']->getData();
                $email = $user->getEmail();
                $checkEmailExist = $em->getRepository(User::class)->findBy(['email' => $email]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $formErrors['invalidEmail'] = 'Invalid email';
                } elseif ($checkEmailExist && $currentEmail !== $email) {
                    $formErrors['invalidEmail'] = 'This email already exists';
                } elseif (!$encoder->isPasswordValid($user, $plainPassword)) {
                    $formErrors['invalidPassword'] = 'Old password is not correct';
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
                    $profilePicture = $associate->getProfilePicture();
                    if ($profilePicture) {
                        $profilePicture->setUploadedFileReference(null);
                    }
                }
            }
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $phoneNumber = $phoneUtil->parse($user->getAssociate()->getMobilePhone(), 'GB');
                $form->get('associate')->get('mobilePhone')->get('number')->setData($phoneNumber->getNationalNumber());
                $form->get('associate')->get('mobilePhone')->get('country')
                    ->setData($phoneUtil->getRegionCodeForCountryCode($phoneNumber->getCountryCode()));
                $number = $form->get('associate')->get('mobilePhone')->get('number')->getData();
                $country = $form->get('associate')->get('mobilePhone')->get('country')->getData();
            } catch (NumberParseException $e) {
                $formErrors['phoneError'] = 'Prior mobile is invalid';
            }
        }

        $associate = $user->getAssociate();

        $em->refresh($user);
        $em->refresh($associate);

        $errors = $form->getErrors(true);

        foreach ($errors as $error) {
            $formErrors[$error->getOrigin()->getName()] = $error->getMessage();
        }

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), $associateNormalizer, new JsonEncoder()]
        );

        $serializedAssociate = $serializer->normalize(
            $associate,
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
            'associate' => $serializedAssociate,
            'formErrors' => $formErrors,
            'updated' => $updated,
            'mobilePhone' => [
                'country' => $country,
                'number' => $number,
            ]
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Post(
     *     path="/api/associate/invite",
     *     @OA\Parameter(
     *         in="path",
     *         name="invitationId",
     *         required=false,
     *         description="Received invitationId querry param to resend invitation",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Received page querry param",
     *         @OA\Schema(type="int", default="1")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="email",
     *         required=false,
     *         description="Email to send invitation to",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="fullName",
     *         required=false,
     *         description="Full Name to send invitation to",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="200",
     *     description="Returns information about associate invitation page or sends new invitation")
     * )
     */

    /**
     * @Rest\Post("/associate/invite", name="associate_invite")
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @param BlacklistManager $blacklistManager
     * @param LoggerInterface $databaseLogger
     * @param AssociateNormalizer $associateNormalizer
     * @param RecaptchaManager $recaptchaManager
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
        RecaptchaManager $recaptchaManager,
        string $siteKey,
        string $secretKey
    ) {
        $formErrors = [];
        $invitationId = null;
        $page = 1;

        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $associate = $user->getAssociate();

        $uniqueAssociateUsername= $associate->getInvitationUserName();

        $uniqueAssociateInvitationLink = $invitationManager->getAssociateUrl($uniqueAssociateUsername);

        if (array_key_exists('page', $request->request->all())) {
            $page = $request->request->all()['page'];
        }

        if (!is_numeric($page)) {
            throw new WrongPageNumberException();
        }

        if (array_key_exists('invitationId', $request->request->all())) {
            $invitationId = $request->request->all()['invitationId'];
        }

        $invitationRepository = $em->getRepository(Invitation::class);

        if ($invitationId) {
            $checkInvitation = $invitationRepository->findOneBy(
                ['sender' => $associate, 'id' => $invitationId]
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

        $form = $this->createForm(InvitationType::class);
        if (array_key_exists('email', $request->request->all())
            && array_key_exists('fullName', $request->request->all())
        ) {
            $form->submit($request->request->all());
        }

        if ($form->isSubmitted() && $form->isValid() || $invitationId) {
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
            $recaptchaResponse = $request->request->get('verifyResponseKey');
            $recaptchaError = $recaptchaManager->validateRecaptcha($recaptchaResponse, $secretKey);

            if ($recaptchaError && !$invitationId) {
                $formErrors['generalError'] = $recaptchaError;
            } elseif ($associateRepo->findAssociatesFilterCount(((new AssociateFilter())->setEmail($email))) > 0) {
                $formErrors['invalidEmail'] = 'Associate already exists';
            } elseif ($blacklistManager->existsInBlacklist($email)) {
                $formErrors['invalidEmail'] = 'The person with this email has opted out of this service';
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
        if ((string)$form['email']->getErrors()) {
            $emailError = $form['email']->getErrors()['0']->getMessage();
            $formErrors['invalidEmail'] = $emailError;
        }
        if ((string)$form['fullName']->getErrors()) {
            $fullNameError = $form['fullName']->getErrors()['0']->getMessage();
            $formErrors['invalidFullName'] = $fullNameError;
        }

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), new ObjectNormalizer(), new JsonEncoder()]
        );

        $serializedInvitations = $serializer->normalize(
            $invitations,
            null,
            ['attributes' => ['id', 'email', 'fullName', 'used', 'created']]
        );

        $data = [
            'formErrors' => $formErrors,
            'invitations' => $serializedInvitations,
            'pagination' => [
                'numberOfPages' => $numberOfPages,
                'currentPage' => $page
            ],
            'uniqueAssociateInvitationLink' => $uniqueAssociateInvitationLink,
            'siteKey' => $siteKey,
            'submitLabel' => 'send'
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Get(
     *     path="/api/associate/downline",
     *     @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=true,
     *         description="Assocaite id"
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Response(response="200",
     *     description="Returns information about given associate id direct downline")
     * )
     */

    /**
     * @Rest\Get("/associate/downline", name="direct_downline")
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
     * @OA\Post(
     *     path="/api/admin/me",
     *     @OA\Parameter(
     *         in="formData",
     *         name="token",
     *         required=true,
     *         description="JWT token",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="200",
     *     description="Gets associate")
     * )
     */

    /**
     * @Rest\Post("/associate/me", name="me")
     * @param Request $request
     * @param JWTManager $JWTManager
     * @param AssociateNormalizer $associateNormalizer
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAssociate(
        Request $request,
        JWTManager $JWTManager,
        AssociateNormalizer $associateNormalizer
    ) {
        $serializedAssociate = null;
        $em = $this->getDoctrine()->getManager();

        $tokenData = $request->request->all();
        $payload = $JWTManager->getPayload($tokenData['token']);

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['email' => $payload]);
        $associate = $user->getAssociate();

        $associateSerializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $associateNormalizer]);
        $serializedAssociate = $associateSerializer->normalize(
            $associate,
            null,
            ['attributes' =>
                [
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
                ]
            ]
        );

        $payload = [
            'associate' => $serializedAssociate,
        ];

        return new JsonResponse($payload, JsonResponse::HTTP_OK);
    }
}
