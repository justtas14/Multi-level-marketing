<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Configuration;
use App\Filter\AssociateFilter;
use App\Form\ChangeContentType;
use App\Form\EmailTemplateType;
use App\Form\EndPrelaunchType;
use App\Form\UserSearchType;
use App\Service\AssociateManager;
use App\Service\ConfigurationManager;
use App\Service\EmailTemplateManager;
use App\Entity\UpdateProfile;
use App\Entity\User;
use App\Form\UpdateProfileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AdminController extends AbstractController
{
    const ASSOCIATE_LIMIT = 20;

    /**
     * @Route("/admin", name="admin")
     * @param AssociateManager $associateManager
     * @param ConfigurationManager $cm
     * @return Response
     */
    public function index(AssociateManager $associateManager, ConfigurationManager $cm)
    {
        /**
         * @var User $user
         */

        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $logo = null;
        if ($configuration) {
            $logo = $configuration->getMainLogo();
        }

        $user = $this->getUser();

        $profilePicture = $user->getAssociate()->getProfilePicture();

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
     * @Route("/admin/emailtemplates", name="email_template")
     */
    public function emailTemplate(Request $request, EmailTemplateManager $emailTemplateManager)
    {
        $em = $this->getDoctrine()->getManager();

        $emailTemplate = $emailTemplateManager->getEmailTemplate(EmailTemplateManager::EMAIL_TYPE_INVITATION);

        $form = $this->createForm(EmailTemplateType::class, $emailTemplate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($emailTemplate);
            $em->flush();

            $this->addFlash('success', 'Template updated');
        }

        return $this->render('admin/emailTemplate.html.twig', [
            'form' => $form->createView()
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
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/changecontent", name="change_content")
     * @param Request $request
     * @param ConfigurationManager $cm
     * @return Response
     */
    public function changeContent(Request $request, ConfigurationManager $cm)
    {
        $em = $this->getDoctrine()->getManager();

        $configuration = $cm->getConfiguration();

        $savedMainLogo = null;
        if ($configuration->getMainLogo() !== null) {
            $savedMainLogo = $configuration->getMainLogo();
            $configuration->setMainLogo(null);
        }

        $form = $this->createForm(ChangeContentType::class, $configuration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($configuration);
            $em->flush();
            $this->addFlash('success', 'Content changed');
        }

        $em->refresh($configuration);
        return $this->render('admin/changeContent.html.twig', [
            'form' => $form->createView()
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
}
