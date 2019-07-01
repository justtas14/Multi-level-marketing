<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Configuration;
use App\Exception\WrongPageNumberException;
use App\Filter\AssociateFilter;
use App\Form\ChangeContentType;
use App\Form\EmailTemplateType;
use App\Form\EndPrelaunchType;
use App\Form\UserSearchType;
use App\Repository\AssociateRepository;
use App\Service\AssociateManager;
use App\Service\ConfigurationManager;
use App\Service\EmailTemplateManager;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serialize;
use Symfony\Component\Form\Form;
use Symfony\Component\Serializer\Serializer;

class AdminController extends AbstractController
{
    const ASSOCIATE_LIMIT = 10;

    /**
     * @Route("/admin", name="admin")
     * @param AssociateManager $associateManager
     * @param ConfigurationManager $cm
     * @return Response
     */
    public function index(AssociateManager $associateManager, ConfigurationManager $cm)
    {
        $configuration = $cm->getConfiguration();

        $logo = $configuration->getMainLogo();

        $user = $this->getUser();

        $profilePicture = null;
        if ($user->getAssociate()) {
            $profilePicture = $user->getAssociate()->getProfilePicture();
        }

        $level = $associateManager->getNumberOfLevels();

        $associateInLevels = [];

        for ($i = 1; $i <= $level; $i++) {
            $associateInLevels[$i] = $associateManager->getNumberOfAssociatesInDownline(
                $i
            );
        }

        return $this->render('admin/index.html.twig', [
            'associatesInLevels' => $associateInLevels,
            'logo' => $logo,
            'profilePicture' => $profilePicture
        ]);
    }

    /**
     * @Route("/admin/emailtemplateslist", name="email_template_list")
     */
    public function emailTemplateList()
    {
        return $this->render('admin/emailTemplateList.html.twig', [

        ]);
    }

    /**
     * @Route("/admin/emailtemplate/{type}", name="email_template")
     * @param Request $request
     * @param EmailTemplateManager $emailTemplateManager
     * @return Response
     * @throws \App\Exception\UnsupportedEmailTypeException
     */
    public function emailTemplate($type, Request $request, EmailTemplateManager $emailTemplateManager)
    {
        $em = $this->getDoctrine()->getManager();
        $title = "";
        $emailTemplate = [];
        $availableParameters = [];

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
                $this->addFlash('error', 'Please do not leave empty values');
            } else {
                $em->persist($emailTemplate);
                $em->flush();

                $this->addFlash('success', 'Template updated');
            }
        }

        return $this->render('admin/emailTemplate.html.twig', [
            'form' => $form->createView(),
            'title' => $title,
            'emailBody' => $emailTemplate->getEmailBody(),
            'availableParameters' => $availableParameters,
        ]);
    }

    /**
     * @Route("/admin/endprelaunch", name="end_prelaunch")
     * @param Request $request
     * @param ConfigurationManager $cm
     * @return Response
     */
    public function endPrelaunch(Request $request, ConfigurationManager $cm)
    {
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $configurationContent = $configuration->getLandingContent();

        $form = $this->createForm(EndPrelaunchType::class, $configuration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($configuration);
            $em->flush();
            if ($configuration->hasPrelaunchEnded()) {
                $this->addFlash('success', 'Prelaunch ended');
            }
        }

        return $this->render('admin/endPrelaunch.html.twig', [
            'form' => $form->createView(), 'configurationContent' => $configurationContent
        ]);
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
     */
    public function changeContent(Request $request, ConfigurationManager $cm, GaufretteFileManager $fileManager)
    {
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $tempConfiguration = $this->createTempConfiguration($configuration);

        $form = $this->createForm(ChangeContentType::class, $tempConfiguration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            if ($tempConfiguration->getTosDisclaimer()) {
                $configuration->setTosDisclaimer($tempConfiguration->getTosDisclaimer());
            }
            $em->persist($configuration);
            $em->flush();
            $this->addFlash('success', 'Content changed');
        }

        $em->refresh($configuration);
        return $this->render('admin/changeContent.html.twig', [
            'form' => $form->createView(),
            'configuration' => $configuration
        ]);
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
                    ->findBy([], [], self::ASSOCIATE_LIMIT, self::ASSOCIATE_LIMIT*$count);
            }
            fclose($df);
        });
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename=associates.csv');

        return $response->send();
    }

    /**
     * @Route("/admin/api/associates", name="user_search_associates")
     * @param int $page
     * @param Request $request
     * @return JsonResponse
     * @throws ExceptionInterface
     * @throws WrongPageNumberException
     */
    public function findAssociates(Request $request)
    {
        $nameField = $request->get('nameField');
        $emailField = $request->get('emailField');
        $telephoneField = $request->get('telephoneField');
        $page = $request->get('page', 1);

        $filter = new AssociateFilter();

        $filter->setEmail($emailField);
        $filter->setFullName($nameField);
        $filter->setTelephone($telephoneField);

        $associateRepository = $this->getDoctrine()->getRepository(Associate::class);

        $countAssociates = $associateRepository->findAssociatesFilterCount($filter);

        $numberOfPages = ceil($countAssociates / self::ASSOCIATE_LIMIT);

        if ($page > $numberOfPages || $page < 0 || !is_numeric($page)) {
            throw new WrongPageNumberException('Page '.$page.' doesnt exist');
        }


        $limitedAssociates = $associateRepository->findAssociatesByFilter(
            $filter,
            self::ASSOCIATE_LIMIT,
            self::ASSOCIATE_LIMIT * ($page-1)
        );

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), new ObjectNormalizer()]);
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
     * @Route("/admin/usersearch", name="user_search")
     * @return Response
     */
    public function userSearch()
    {
        $form = $this->createForm(UserSearchType::class);

        return $this->render('admin/usersearch.html.twig', [
            'usersearch' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/api/explorer", name="api_admin_explorer")
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
}
