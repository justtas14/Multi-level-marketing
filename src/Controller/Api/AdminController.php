<?php

namespace App\Controller\Api;

use App\CustomNormalizer\AssociateNormalizer;
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
use App\Form\UserSearchType;
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

/**
 * @Route("/api", name="api_")
 */

class AdminController extends AbstractController
{
    const ASSOCIATE_LIMIT = 10;
    const INVITATION_LIMIT = 10;
    const LOG_LIMIT = 20;
    const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'bmp', 'gif', 'png', 'webp', 'ico'];

    /**
     * @Route("/admin/home", name="admin")
     * @param AssociateManager $associateManager
     * @param ConfigurationManager $cm
     * @param GaufretteFileManager $gaufretteFileManager
     * @return Response
     */
    public function index(
        AssociateManager $associateManager,
        ConfigurationManager $cm,
        GaufretteFileManager $gaufretteFileManager
    ) {
        $configuration = $cm->getConfiguration();

        $logo = $configuration->getMainLogo();
        $logoPath = $gaufretteFileManager->generateDownloadUrl($logo);

        $user = $this->getUser();

        $profilePicture = null;
        $profilePicturePath = null;
        if ($user->getAssociate()) {
            $profilePicture = $user->getAssociate()->getProfilePicture();
            $profilePicturePath = $gaufretteFileManager->generateDownloadUrl($profilePicture);
        }

        $level = $associateManager->getNumberOfLevels();

        $associateInLevels = [];

        for ($i = 1; $i <= $level; $i++) {
            $associateInLevels[$i] = $associateManager->getNumberOfAssociatesInDownline(
                $i
            );
        }

        $data = [
            'associatesInLevels' => $associateInLevels,
            'logoPath' => $logoPath,
            'profilePicturePath' => $profilePicturePath
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/emailtemplate/{type}", name="email_template")
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
        $formErrors = [];
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

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$emailTemplate->getEmailBody() || !$emailTemplate->getEmailSubject()) {
                $formErrors = ['emptyValues' => 'Please do not leave empty values'];
            } else {
                $em->persist($emailTemplate);
                $em->flush();
                $formSuccess = true;
            }
        }

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), new ObjectNormalizer(), new JsonEncoder()]
        );

        $serializedTemplate = $serializer->normalize(
            $emailTemplate,
            null,
            ['attributes' => ['id', 'emailSubject', 'emailBody', 'emailType']]
        );

        $data = [
            'formSuccess' => $formSuccess,
            'formErrors' => $formErrors,
            'emailTemplate' => $serializedTemplate,
            'title' => $title,
            'emailBody' => $emailTemplate->getEmailBody(),
            'availableParameters' => $availableParameters
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/endprelaunch", name="end_prelaunch")
     * @param Request $request
     * @param ConfigurationManager $cm
     * @return Response
     */
    public function endPrelaunch(Request $request, ConfigurationManager $cm, LoggerInterface $databaseLogger)
    {
        $formSuccess = true;
        $prelaunchEnded = true;
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $configurationContent = $configuration->getLandingContent();

        $form = $this->createForm(EndPrelaunchType::class, $configuration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($configuration);
            $em->flush();
            if ($configuration->hasPrelaunchEnded()) {
                $prelaunchEnded = true;
                $databaseLogger->info('Prelaunch successfully ended');
            }
            $formSuccess = true;
        }

        $data = [
            'prelaunchEnded' => $prelaunchEnded,
            'formSuccess' => $formSuccess,
            'configurationContent' => $configurationContent
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
     * @Route("/admin/changecontent", name="change_content")
     * @param Request $request
     * @param ConfigurationManager $cm
     * @return Response
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function changeContent(Request $request, ConfigurationManager $cm, GaufretteFileManager $fileManager)
    {
        $contentChanged = false;
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $tempConfiguration = $this->createTempConfiguration($configuration);

        $form = $this->createForm(ChangeContentType::class, $tempConfiguration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainLogoFileId = $form['hiddenMainLogoFile']->getData();
            $termsOfServicesFileId = $form['hiddenTermsOfServiceFile']->getData();
            if ($mainLogoFileId || $termsOfServicesFileId) {
                if ($mainLogoFileId) {
                    $originalFile = $em->getRepository(Gallery::class)->find($mainLogoFileId)->getGalleryFile();
                    $configuration->setMainLogo($originalFile);
                }
                if ($termsOfServicesFileId) {
                    $originalFile = $em->getRepository(Gallery::class)->find($termsOfServicesFileId)->getGalleryFile();
                    $configuration->setTermsOfServices($originalFile);
                }
            } else {
                if ($tempConfiguration->getMainLogo()) {
                    if ($configuration->getMainLogo()) {
                        $logo = $configuration->getMainLogo();
                        $configuration->setMainLogo(null);
                        $fileManager->removeEntity($logo);
                    }
                    $configuration->setMainLogo($tempConfiguration->getMainLogo());
                }
                if ($tempConfiguration->getTermsOfServices()) {
                    if ($configuration->getTermsOfServices()) {
                        $temp = $configuration->getTermsOfServices();
                        $configuration->setTermsOfServices(null);
                        $fileManager->removeEntity($temp);
                    }
                    $configuration->setTermsOfServices($tempConfiguration->getTermsOfServices());
                }
            }
            if ($tempConfiguration->getTosDisclaimer()) {
                $configuration->setTosDisclaimer($tempConfiguration->getTosDisclaimer());
            }
            $em->persist($configuration);
            $em->flush();
            $contentChanged = true;
        }

        $serializer = new Serializer(
            [new DateTimeNormalizer('Y-m-d h:i:s'), new ObjectNormalizer(), new JsonEncoder()]
        );

        $serializedConfiguration = $serializer->normalize(
            $configuration,
            null,
            ['attributes' => [
                'id',
                'mainLogo',
                'termsOfService',
                'landingContent',
                'hasPrelaunchEnded',
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
     * @Route("/admin/csv", name="csv")
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
     * @Route("/admin/explorer", name="api_admin_explorer")
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
     * @Route("/admin/users/{id}", name="user_search_details")
     * @param $id
     * @param Request $request
     * @param AssociateManager $associateManager
     * @param AssociateNormalizer $associateNormalizer
     * @return Response
     * @throws ExceptionInterface
     */
    public function userSearchDetails(
        $id,
        Request $request,
        AssociateManager $associateManager,
        AssociateNormalizer $associateNormalizer
    ) {
        $formError = '';
        $formSuccess = '';
        /** @var User $user */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $page = $request->get('page', 1);

        if (!is_numeric($page)) {
            throw new WrongPageNumberException();
        }
        /** @var Associate $associateToDisplay */
        $associateToDisplay = $associateManager->getAssociate($id);

        $form = $this->createForm(AssociateModificationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $associateParentId = $form['associateParentId']->getData();
            $associateId = $form['associateId']->getData();
            $deleteAssociateId = $form['deleteAssociateId']->getData();
            if ($associateParentId != null && $associateId != null) {
                if ($associateParentId === $associateId) {
                    $formError = 'Cannot change associate parent';
                } elseif ($associateManager->isAncestor($associateParentId, $associateId, false)) {
                    $formError = 'Cannot change associate parent';
                } else {
                    $associateManager->changeAssociateParent($associateId, $associateParentId);
                    $formSuccess = 'Parent successfully changed';
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
                    }
                }
            }
        }

        $user = $em->getRepository(User::class)->findOneBy(['associate' => $associateToDisplay]);

        $invitationRepository = $em->getRepository(Invitation::class);

        $allInvitations = $invitationRepository->findBy(['sender' => $user]);

        $numberOfPages = $this->numberOfPages(count($allInvitations), self::INVITATION_LIMIT, $page);

        $invitations = $invitationRepository->findBy(
            ['sender' => $user],
            ['created' => 'DESC'],
            self::INVITATION_LIMIT,
            self::INVITATION_LIMIT * ($page-1)
        );

        if (!$associateToDisplay) {
            throw new NotFoundHttpException("User with id ".$id." is not found");
        }

        $level = $associateManager->getNumberOfLevels($associateToDisplay->getAssociateId());

        $associateInLevels = [];

        for ($i = 1; $i <= $level; $i++) {
            $associateInLevels[$i] = $associateManager->getNumberOfAssociatesInDownline(
                $i,
                $associateToDisplay->getAssociateId()
            );
        }

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $associateNormalizer]);
        $serializedAssociate = $serializer->normalize(
            $associateToDisplay,
            null,
            ['attributes' => ['fullName', 'email', 'mobilePhone', 'level', 'joinDate', 'id']]
        );

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), new ObjectNormalizer()]);
        $serializedInvitations = $serializer->normalize(
            $invitations,
            null,
            ['attributes' => ['id', 'email', 'fullName', 'sender', 'used', 'created']]
        );


        $data = [
            'formError' => $formError,
            'formSuccess' => $formSuccess,
            'associate' => $serializedAssociate,
            'invitations' => $serializedInvitations,
            'associatesInLevels' => $associateInLevels,
            'pagination' => [
                'currentPage' => $page,
                'numberOfPages' => $numberOfPages
            ]
        ];

        return new JsonResponse($data);
    }


    /**
     * @Route("/admin/jsonGallery", name="json_gallery")
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
            'imageExtensions' => self::IMAGE_EXTENSIONS,
            'imageTypes' => $this->getImageTypes(),
            'pagination' => [
                'numberOfPages' => $numberOfPages,
                'currentPage' => $page,
            ]
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/removeFile", name="remove_file")
     * @param Request $request
     * @param GaufretteFileManager $gaufretteFileManager
     * @return JsonResponse|Response
     */
    public function removeFile(Request $request, GaufretteFileManager $gaufretteFileManager)
    {
        if ($request->isMethod('POST')) {
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
        } else {
            return new Response('', 200);
        }
    }


    /**
     * @Route("/admin/uploadFile", name="upload_file")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function uploadEditorImage(
        Request $request
    ) {
        if ($request->isMethod('POST')) {
            $filePath = $request->getContent();

            $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            $absoluteUrl = $baseUrl . $filePath;

            return new JsonResponse($absoluteUrl, 200);
        } else {
            return new Response('', 200);
        }
    }

    /**
     * @Route("/admin/uploadGalleryFile", name="upload_gallery_file")
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
        } else {
            return new Response('', 200);
        }
    }

    /**
     * @Route("/admin/get-logs", name="get-logs")
     * @param Request $request
     * @return Response
     * @throws ExceptionInterface
     */
    public function getLogs(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $logRepository = $em->getRepository(Log::class);

        $page = $request->get('page', 1);

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
