<?php

namespace App\Controller\Api;

use App\CustomNormalizer\AssociateNormalizer;
use App\CustomNormalizer\ConfigurationNormalizer;
use App\Entity\Associate;
use App\Entity\Configuration;
use App\Entity\Gallery;
use App\Entity\Invitation;
use App\Entity\Log;
use App\Entity\User;
use App\Exception\WrongPageNumberException;
use App\Filter\AssociateFilter;
use App\Form\ChangeContentType;
use App\Form\AssociateModificationType;
use App\Form\EditorImageType;
use App\Form\EmailTemplateType;
use App\Form\EndPrelaunchType;
use App\Repository\AssociateRepository;
use App\CustomNormalizer\GalleryNormalizer;
use App\Service\AssociateManager;
use App\Service\ConfigurationManager;
use App\Service\EmailTemplateManager;
use Doctrine\ORM\EntityManagerInterface;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @OA\Info(title="Admin API", version="0.1")
 * @Rest\Route("/api", name="_api")
 */

class AdminController extends AbstractController
{
    const ASSOCIATE_LIMIT = 10;
    const INVITATION_LIMIT = 10;
    const LOG_LIMIT = 20;

    /**
     * @OA\Get(
     *     path="/api/admin",
     *     @OA\Response(response="200", description="Admin home page")
     * )
     */

    /**
     * @Rest\Get("/admin", name="admin")
     * @Route("/admin", name="admin")
     * @param AssociateManager $associateManager
     * @return Response
     */
    public function index(
        AssociateManager $associateManager
    ) {
        $level = $associateManager->getNumberOfLevels();
        $maxLevel = 1;

        $associateInLevels = [];

        for ($i = 1; $i <= $level; $i++) {
            $associateInLevels[$i] = $associateManager->getNumberOfAssociatesInDownline(
                $i
            );
        }

        if (sizeof($associateInLevels) > 0) {
            $maxLevel = max($associateInLevels);
        }

        $data = [
            'associatesInLevels' => $associateInLevels,
            'levels' => $level,
            'maxLevel' => $maxLevel
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/emailtemplate/{type}",
     *     @OA\Parameter(
     *         in="path",
     *         name="type",
     *         required=true,
     *         description="Email template type"
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="emailBody",
     *         required=true,
     *         description="Email body string",
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *         in="formData",
     *         name="emailSubject",
     *         required=true,
     *         description="Email subject string",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="200",
     *      description="Returns email template info in json depending on given in params type ")
     * )
     */

    /**
     * @Rest\Post("/admin/emailtemplate/{type}", name="email_template")
     * @param Request $request
     * @param EmailTemplateManager $emailTemplateManager
     * @return Response
     * @throws \App\Exception\UnsupportedEmailTypeException
     * @throws ExceptionInterface
     */
    public function emailTemplate($type, Request $request, EmailTemplateManager $emailTemplateManager)
    {
        $em = $this->getDoctrine()->getManager();
        $title = "";
        $formError = '';
        $emailTemplate = [];
        $availableParameters = [];
        $formSuccess = false;

        switch ($type) {
            case 'invitation':
                $emailTemplate =
                    $emailTemplateManager->getEmailTemplate(EmailTemplateManager::EMAIL_TYPE_INVITATION);
                $title = 'Invitation email template';
                $availableParameters = [
                    'Sender name' => '{{ senderName }}',
                    'Receiver name' => '{{ receiverName }}',
                    'Invitation link' => '{{ link }}',
                    'Opt out of service link' => '{{ optOutUrl }}'
                ];
                break;
            case 'password':
                $emailTemplate =
                    $emailTemplateManager->getEmailTemplate(EmailTemplateManager::EMAIL_TYPE_RESET_PASSWORD);
                $title = 'Reset password email template';
                $availableParameters = ['Reset password link' => '{{ link }}'];
                break;
            case 'welcome':
                $emailTemplate =
                    $emailTemplateManager->getEmailTemplate(EmailTemplateManager::EMAIL_TYPE_WELCOME);
                $title = 'Welcome email template';
                $availableParameters = ['Full user name' => '{{ name }}'];
                break;
            default:
                throw new NotFoundHttpException("Email template does not exist!");
        }

        $form = $this->createForm(EmailTemplateType::class, $emailTemplate);

        $formData = $request->request->all();

        if ($formData) {
            if (!$formData['emailBody'] || !$formData['emailSubject']) {
                $formError = 'Please do not leave empty values';
            } else {
                $form->submit($formData);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($emailTemplate);
            $em->flush();
            $formSuccess = true;
        }

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), new ObjectNormalizer(), new JsonEncoder()]
        );

        $serializedTemplate = $serializer->normalize(
            $emailTemplate,
            null,
            ['attributes' => ['emailSubject', 'emailBody']]
        );

        $data = [
            'formSuccess' => $formSuccess,
            'formError' => $formError,
            'emailTemplate' => $serializedTemplate,
            'title' => $title,
            'availableParameters' => $availableParameters
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/endprelaunch",
     *     @OA\Parameter(
     *         in="formData",
     *         name="hasPrelaunchEnded",
     *         required=false,
     *         description="Flag value which sets prelaunch end",
     *         @OA\Schema(type="boolean")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="landingContent",
     *         required=false,
     *         description="Landing content string",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="200", description="End prelaunch info in json format")
     * )
     */

    /**
     * @Rest\Post("/admin/endprelaunch", name="end_prelaunch")
     * @Route("/admin/endprelaunch", name="end_prelaunch")
     * @param Request $request
     * @param ConfigurationManager $cm
     * @return Response
     */
    public function endPrelaunch(Request $request, ConfigurationManager $cm, LoggerInterface $databaseLogger)
    {
        $formSuccess = false;
        $errorMessage = '';
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $form = $this->createForm(EndPrelaunchType::class, $configuration);

        $formData = $request->request->all();

        if ($formData) {
            $formData['prelaunchEnded'] = $formData['prelaunchEnded'] === 'true' ? true : false;
            $form->submit($formData);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$configuration->getLandingContent()) {
                $errorMessage = 'Landing content cannot be empty!';
            } else {
                $em->persist($configuration);
                $em->flush();
                if ($configuration->hasPrelaunchEnded()) {
                    $databaseLogger->info('Prelaunch successfully ended');
                }
                $formSuccess = true;
            }
        }

        $configurationContent = $configuration->getLandingContent();
        $hasPrelaunchEnded = $configuration->hasPrelaunchEnded();

        $data = [
            'errorMessage' => $errorMessage,
            'formSuccess' => $formSuccess,
            'configurationContent' => $configurationContent,
            'hasPrelaunchEnded' => $hasPrelaunchEnded
        ];

        return new JsonResponse($data);
    }


    private function createTempConfiguration(Configuration $configuration)
    {
        $tempConfiguration = new Configuration();
        $tempConfiguration->setTosDisclaimer($configuration->getTosDisclaimer());
//        if ($configuration->getTermsOfServices()) {
//            $tempConfiguration->setTermsOfServices($configuration->getTermsOfServices());
//        }
//        if ($configuration->getMainLogo()) {
//            $tempConfiguration->setMainLogo($configuration->getMainLogo());
//        }
        return $tempConfiguration;
    }

    /**
     * @OA\Post(
     *     path="/api/admin/changecontent",
     *      @OA\Parameter(
     *         in="formData",
     *         name="hiddenMainLogoFile",
     *         required=false,
     *         description="Main logo file id",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="hiddenTermsOfServiceFile",
     *         required=false,
     *         description="Terms of services file id",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="tosDisclaimer",
     *         required=false,
     *         description="Terms Of Service Disclaimer",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="200", description="Change content page info in json format")
     * )
     */

    /**
     * @Rest\Post("/admin/changecontent", name="change_content")
     * @param Request $request
     * @param ConfigurationManager $cm
     * @param ConfigurationNormalizer $configurationNormalizer
     * @param GaufretteFileManager $fileManager
     * @return Response
     * @throws ExceptionInterface
     */
    public function changeContent(
        Request $request,
        ConfigurationManager $cm,
        ConfigurationNormalizer $configurationNormalizer,
        GaufretteFileManager $fileManager
    ) {
        $contentChanged = false;
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $tempConfiguration = $this->createTempConfiguration($configuration);

        $form = $this->createForm(ChangeContentType::class, $tempConfiguration);

        $formData = $request->request->all();

        if ($formData) {
            $form->submit($formData);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $mainLogoFileId = $form['hiddenMainLogoFile']->getData();
            $termsOfServicesFileId = $form['hiddenTermsOfServiceFile']->getData();
            if ($mainLogoFileId || $termsOfServicesFileId) {
                if ($mainLogoFileId) {
                    $originalFile = $em->getRepository(Gallery::class)->find($mainLogoFileId)->getGalleryFile();
                    if (!$originalFile->getContextValue('public')) {
                        $originalFile->addContext('public', 1);
                    }
                    $configuration->setMainLogo($originalFile);
                }
                if ($termsOfServicesFileId) {
                    $originalFile = $em->getRepository(Gallery::class)->find($termsOfServicesFileId)->getGalleryFile();
                    $configuration->setTermsOfServices($originalFile);
                }
            }
            if ($tempConfiguration->getTosDisclaimer()) {
                $configuration->setTosDisclaimer($tempConfiguration->getTosDisclaimer());
            }
            $em->persist($configuration);
            $em->flush();
            $contentChanged = true;
        }

        $em->refresh($configuration);

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), $configurationNormalizer, new JsonEncoder()]
        );

        $serializedConfiguration = $serializer->normalize(
            $configuration,
            null,
            ['attributes' => [
                'tosDisclaimer'
            ]]
        );


        $data = [
            'configuration' => $serializedConfiguration,
            'contentChanged' => $contentChanged
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/associates",
     *      @OA\Parameter(
     *         in="query",
     *         name="nameField",
     *         required=false,
     *         description="Received name field querry param",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *         in="query",
     *         name="emailField",
     *         required=false,
     *         description="Received email field querry param",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *         in="query",
     *         name="telephoneField",
     *         required=false,
     *         description="Received telephone field querry param",
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Received page querry param",
     *         @OA\Schema(type="int", default="1")
     *      ),
     *     @OA\Response(response="200", description="Returns appropriate associates and pagination in json")
     * )
     */

    /**
     * @Rest\Get("/admin/associates", name="user_search_associates")
     * @Route("/admin/associates", name="user_search_associates")
     * @param Request $request
     * @param AssociateNormalizer $associateNormalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function findAssociates(Request $request, AssociateNormalizer $associateNormalizer)
    {
        $nameField = $request->get('nameField');
        $emailField = $request->get('emailField');
        $telephoneField = $request->get('telephoneField');
        $page = $request->get('page', 1);

        if (!is_numeric($page)) {
            throw new WrongPageNumberException();
        }

        $filter = new AssociateFilter();

        $filter->setEmail($emailField);
        $filter->setFullName($nameField);
        $filter->setTelephone($telephoneField);

        $associateRepository = $this->getDoctrine()->getRepository(Associate::class);

        $countAssociates = $associateRepository->findAssociatesFilterCount($filter);

        $numberOfPages = $this->numberOfPages($countAssociates, self::ASSOCIATE_LIMIT, $page);

        $limitedAssociates = $associateRepository->findAssociatesByFilter(
            $filter,
            self::ASSOCIATE_LIMIT,
            self::ASSOCIATE_LIMIT * ($page - 1)
        );

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $associateNormalizer]);
        $serializedAssociates = $serializer->normalize(
            $limitedAssociates,
            null,
            ['attributes' => ['fullName', 'email', 'mobilePhone', 'level', 'joinDate', 'id']]
        );

        $data = [
            'associates' => $serializedAssociates,
            'pagination' => [
                'maxPages' => $numberOfPages,
                'currentPage' => $page,
            ]
        ];

        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/csv",
     *     @OA\Response(response="200", description="Download all associates in txt file")
     * )
     */

    /**
     * @Rest\Get("/admin/csv", name="csv")
     * @return StreamedResponse
     */
    public function exportToCsv()
    {
        $associateRepository = $this->getDoctrine()->getManager()->getRepository(Associate::class);

        $response = new StreamedResponse(function () use ($associateRepository) {
            $df = fopen('php://output', 'w');
            $associates = $associateRepository->findBy([], [], self::ASSOCIATE_LIMIT, 0);
            fputcsv($df, array_keys(reset($associates)->toArray()));
            $count = 0;
            while (sizeof($associates)) {
                foreach ($associates as $row) {
                    fputcsv($df, $row->toArray());
                }
                $count = $count + 1;
                $associates = $associateRepository
                    ->findBy([], [], self::ASSOCIATE_LIMIT, self::ASSOCIATE_LIMIT * $count);
            }
            fclose($df);
        });
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename=associates.csv');

        return $response->send();
    }

    /**
     * @OA\Get(
     *     path="/api/admin/explorer",
     *      @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=false,
     *         description="Received id querry param",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Response(response="200", description="Returns information about associate or company and its children")
     * )
     */

    /**
     * @Rest\Get("/admin/explorer", name="api_admin_explorer")
     * @param Request $request
     * @param AssociateManager $associateManager
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getCompanyRoot(
        Request $request,
        AssociateManager $associateManager,
        EntityManagerInterface $em
    ) {
        $id = $request->get('id');
        /** @var AssociateRepository $associateRepo */
        $associateRepo = $em->getRepository(Associate::class);
        if ($id) {
            return new JsonResponse($associateManager->getDirectDownlineAssociates($id), JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(
                [
                    'id' => '-1',
                    'title' => 'Company',
                    'parentId' => '-2',
                    'numberOfChildren' => $associateRepo->findAllDirectAssociatesCount(-1)
                ],
                JsonResponse::HTTP_OK
            );
        }
    }

    private function getImageTypes()
    {
        return [
            'image/png',
            'image/jpeg',
            'image/webp',
        ];
    }

    private function numberOfPages($allFiles, $limit, $page)
    {
        $numberOfPages = ceil($allFiles / $limit);

        if ($numberOfPages == 0) {
            $numberOfPages++;
        }

        if (($page < 1 || $page > $numberOfPages)) {
            throw new WrongPageNumberException('Page ' . $page . ' doesnt exist');
        }
        return $numberOfPages;
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users",
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Received page querry param",
     *         @OA\Schema(type="int", default="1")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="associateId",
     *         required=true,
     *         description="Associate id",
     *         @OA\Schema(type="int")
     *      ),
     *      @OA\Parameter(
     *         in="formData",
     *         name="associateParentId",
     *         required=false,
     *         description="Associate parent id",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Parameter(
     *         in="formData",
     *         name="deleteAssociateId",
     *         required=false,
     *         description="Associate to delete id",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Response(response="200", description="Returns information about specific user")
     * )
     */

    /**
     * @Rest\Post("/admin/users", name="user_search_details")
     * @param Request $request
     * @param AssociateManager $associateManager
     * @param AssociateNormalizer $associateNormalizer
     * @return Response
     * @throws ExceptionInterface
     */
    public function userSearchDetails(
        Request $request,
        AssociateManager $associateManager,
        AssociateNormalizer $associateNormalizer
    ) {
        $formError = '';
        $formSuccess = [
            'type' => '',
            'message' => '',
        ];
        $id = null;
        $successType = null;
        $associateToDisplay = null;

        /** @var User $user */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $page = $request->get('page', 1);

        if ($request->request->all() && array_key_exists('page', $request->request->all())) {
            $page = $request->request->all()['page'];
        }

        if (!is_numeric($page)) {
            throw new WrongPageNumberException();
        }

        if ($request->request->all() && array_key_exists('associateId', $request->request->all())) {
            /** @var Associate $associateToDisplay */
            $associateToDisplay = $associateManager->getAssociate($request->request->all()['associateId']);
            $id = $request->request->all()['associateId'];
        }

        if (!$associateToDisplay) {
            throw new NotFoundHttpException("User with id ".$id." is not found");
        }

        $form = $this->createForm(AssociateModificationType::class);

        if (array_key_exists('associateParentId', $request->request->all())
            || array_key_exists('deleteAssociateId', $request->request->all())
        ) {
            $form->submit($request->request->all());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $associateParentId = $form['associateParentId']->getData();
            $deleteAssociateId = $form['deleteAssociateId']->getData();
            if ($associateParentId != null && $id != null) {
                if ($associateParentId === $id) {
                    $formError = 'Cannot change associate parent';
                } elseif ($associateManager->isAncestor($associateParentId, $id, false)) {
                    $formError = 'Cannot change associate parent';
                } else {
                    $associateManager->changeAssociateParent($id, $associateParentId);
                    $formSuccess = 'Parent successfully changed';
                    $successType = "parent";
                }
            } elseif ($deleteAssociateId != null) {
                $deleteAssociate = $associateManager->getAssociate($deleteAssociateId);
                $loggedInAssociate = $user->getAssociate();

                if ($loggedInAssociate == $deleteAssociate) {
                    $formError = 'You cannot delete yourself';
                } else {
                    $deleteAssociateFullName = $deleteAssociate->getFullName();
                    $deleteAssociateUser = $em->getRepository(User::class)
                        ->findOneBy(['associate' => $deleteAssociate]);
                    $isDeleted = $associateManager->deleteAssociate($deleteAssociate);
                    if (!$isDeleted) {
                        $formError = 'Cannot delete user with children';
                    } else {
                        /** @var User $deleteAssociateUser */
                        $associateManager->deleteUser($deleteAssociateUser);
                        $formSuccess = 'User ' .$deleteAssociateFullName. ' deleted';
                        $successType = "delete";

                        $data = [
                            'formError' => $formError,
                            'formSuccess' => [
                                'message' => $formSuccess,
                                'type' => $successType,
                            ]
                        ];

                        return new JsonResponse($data);
                    }
                }
            }
        }

        $invitationRepository = $em->getRepository(Invitation::class);

        $allInvitations = $invitationRepository->findBy(['sender' => $associateToDisplay]);

        $numberOfPages = $this->numberOfPages(count($allInvitations), self::INVITATION_LIMIT, $page);

        $invitations = $invitationRepository->findBy(
            ['sender' => $associateToDisplay],
            ['created' => 'DESC'],
            self::INVITATION_LIMIT,
            self::INVITATION_LIMIT * ($page-1)
        );

        $level = $associateManager->getNumberOfLevels($associateToDisplay->getAssociateId());

        $associateInLevels = [];
        $maxLevel = 1;

        for ($i = 1; $i <= $level; $i++) {
            $associateInLevels[$i] = $associateManager->getNumberOfAssociatesInDownline(
                $i,
                $associateToDisplay->getAssociateId()
            );
        }
        if (sizeof($associateInLevels)) {
            $maxLevel = max($associateInLevels);
        }

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $associateNormalizer]);
        $serializedAssociate = $serializer->normalize(
            $associateToDisplay,
            null,
            ['attributes' => [
                'id',
                'fullName',
                'email',
                'address',
                'address2',
                'level',
                'dateOfBirth',
                'joinDate',
                'city',
                'postcode',
                'country',
                'mobilePhone',
                'homePhone',
                'agreedToEmailUpdates',
                'agreedToTextMessageUpdates',
                'agreedToSocialMediaUpdates',
                'agreedToTermsOfService',
            ]]
        );

        $associateParent = $associateToDisplay->getParent();

        if ($associateParent->getId() == -1) {
            $serializedParent = null;
        } else {
            $serializedParent = $serializer->normalize(
                $associateParent,
                null,
                ['attributes' => [
                    'id',
                    'fullName',
                    'email',
                    'address',
                    'address2',
                    'city',
                    'level',
                    'postcode',
                    'dateOfBirth',
                    'joinDate',
                    'country',
                    'mobilePhone',
                    'homePhone',
                    'agreedToEmailUpdates',
                    'agreedToTextMessageUpdates',
                    'agreedToSocialMediaUpdates',
                    'agreedToTermsOfService',
                ]]
            );
        }

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), new ObjectNormalizer()]);
        $serializedInvitations = $serializer->normalize(
            $invitations,
            null,
            ['attributes' => ['id', 'email', 'fullName', 'used', 'created']]
        );


        $data = [
            'formError' => $formError,
            'formSuccess' => [
                'message' => $formSuccess,
                'type' => $successType,
            ],
            'associate' => $serializedAssociate,
            'associateParent' => $serializedParent,
            'invitations' => $serializedInvitations,
            'associatesInLevels' => $associateInLevels,
            'maxLevel' => $maxLevel,
            'levels' => $level,
            'pagination' => [
                'currentPage' => $page,
                'numberOfPages' => $numberOfPages
            ]
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/jsonGallery",
     *      @OA\Parameter(
     *         in="query",
     *         name="category",
     *         required=false,
     *         description="Category of gallery files",
     *         @OA\Schema(type="string", default="all")
     *      ),
     *      @OA\Parameter(
     *         in="query",
     *         name="imageLimit",
     *         required=false,
     *         description="Image limit to display in single page",
     *         @OA\Schema(type="int", default="20")
     *      ),
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Received page querry param",
     *         @OA\Schema(type="int", default="1")
     *      ),
     *     @OA\Response(response="200", description="Returns appropriate gallery files and pagination in json format")
     * )
     */

    /**
     * @Rest\Get("/admin/jsonGallery", name="json_gallery")
     * @param Request $request
     * @param GalleryNormalizer $galleryNormalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function jsonGallery(Request $request, GalleryNormalizer $galleryNormalizer)
    {
        $page = $request->get('page', 1);

        if (!is_numeric($page)) {
            throw new WrongPageNumberException();
        }

        if ($page == 0) {
            $page++;
        }

        $category = $request->get('category', 'all');
        $imageLimit = $request->get('imageLimit', 20);

        $em = $this->getDoctrine()->getManager();
        $galleryRepository = $em->getRepository(Gallery::class);

        switch ($category) {
            case 'all':
                $allFiles = $galleryRepository->countAllFiles();
                $files = $galleryRepository->findBy(
                    [],
                    ['created' => 'DESC'],
                    $imageLimit,
                    $imageLimit * ($page - 1)
                );
                break;
            case 'images':
                $allFiles = $galleryRepository->countAllImages($this->getImageTypes());
                $files = $galleryRepository->findByImages(
                    $this->getImageTypes(),
                    $imageLimit,
                    $imageLimit * ($page - 1)
                );
                break;
            case 'files':
                $allFiles = $galleryRepository->countAllNotImages($this->getImageTypes());
                $files = $galleryRepository->findByNotImages(
                    $this->getImageTypes(),
                    $imageLimit,
                    $imageLimit * ($page - 1)
                );
                break;
            default:
                throw new NotFoundHttpException();
        }

        $numberOfPages = $this->numberOfPages($allFiles, $imageLimit, $page);

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $galleryNormalizer]);
        $serializedFiles = $serializer->normalize(
            $files,
            null,
            ['attributes' => ['id', 'galleryFile', 'mimeType', 'created']]
        );

        $data = [
            'files' => $serializedFiles,
            'imageTypes' => $this->getImageTypes(),
            'pagination' => [
                'numberOfPages' => $numberOfPages,
                'currentPage' => $page,
            ]
        ];

        return new JsonResponse($data);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/removeFile",
     *     @OA\Parameter(
     *         in="query",
     *         name="galleryId",
     *         required=true,
     *         description="Gallery file id to remove",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Parameter(
     *         in="query",
     *         name="fileId",
     *         required=true,
     *         description="File id to remove",
     *         @OA\Schema(type="int")
     *      ),
     *     @OA\Response(response="200",
     *     description="Removes completely gallery file with given file and gallery file id")
     * )
     */

    /**
     * @Rest\Delete("/admin/removeFile", name="remove_file")
     * @param Request $request
     * @param GaufretteFileManager $gaufretteFileManager
     * @return JsonResponse|Response
     */
    public function removeFile(Request $request, GaufretteFileManager $gaufretteFileManager)
    {
        $content = $request->getContent();
        $ids = json_decode($content, true);

        $galleryId = $ids['params']['galleryId'];
        $fileId = $ids['params']['fileId'];

        $em = $this->getDoctrine()->getManager();

        /** @var Configuration $cm */
        $cm = $em->getRepository(Configuration::class)->findOneBy([]);

        $fileInUse = false;

        if (($cm->getTermsOfServices() && $cm->getTermsOfServices()->getId() === $fileId)
            || ($cm->getMainLogo() && $cm->getMainLogo()->getId() === $fileId)) {
            $fileInUse = true;
        } else {
            $file = $em->getRepository(\App\Entity\File::class)->find($fileId);
            $galleryFile = $em->getRepository(Gallery::class)->find($galleryId);

            $gaufretteFileManager->remove($file);
            $em->remove($file);
            $em->remove($galleryFile);

            $em->flush();
        }

        return new JsonResponse(
            ['fileInUse' => $fileInUse],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/admin/uploadGalleryFile",
     *     @OA\Parameter(
     *         in="formData",
     *         name="galleryFile",
     *         required=true,
     *         description="Gallery file to upload",
     *         @OA\Schema(type="galleryFile")
     *      ),
     *     @OA\Response(response="200",
     *     description="Uploads and adds gallery file to database")
     * )
     */


    /**
     * @Rest\Post("/admin/uploadGalleryFile", name="upload_gallery_file")
     * @param Request $request
     * @param GalleryNormalizer $galleryNormalizer
     * @return JsonResponse|Response
     * @throws ExceptionInterface
     */
    public function uploadGalleryFile(
        Request $request,
        GalleryNormalizer $galleryNormalizer
    ) {
        $galleryFile = new Gallery();

        $form = $this->createForm(EditorImageType::class, $galleryFile);
        $form->submit($request->files->all());

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * @OA\Get(
     *     path="/api/admin/get-logs",
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Received page querry param",
     *         @OA\Schema(type="int", default="1")
     *      ),
     *     @OA\Response(response="200",
     *     description="Returns appropriate logs depending on querry parameter page")
     * )
     */

    /**
     * @Rest\Get("/admin/get-logs", name="get_logs")
     * @param Request $request
     * @return Response
     * @throws ExceptionInterface
     */
    public function getLogs(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $logRepository = $em->getRepository(Log::class);

        $page = $request->query->all()['page'];

        if (!is_numeric($page)) {
            throw new WrongPageNumberException();
        }

        $allLogs = $logRepository->countAllLogs();
        $logs = $logRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            self::LOG_LIMIT,
            self::LOG_LIMIT * ($page - 1)
        );

        $numberOfPages = $this->numberOfPages($allLogs, self::LOG_LIMIT, $page);

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), new ObjectNormalizer(), new JsonEncoder()]
        );
        $serializedLogs = $serializer->normalize(
            $logs,
            null,
            ['attributes' => ['id', 'message', 'createdAt']]
        );

        $data = [
            'logs' => $serializedLogs,
            'pagination' => [
                'maxPage' => $numberOfPages,
                'currentPage' => $page,
            ]
        ];

        return new JsonResponse($data);
    }
}
