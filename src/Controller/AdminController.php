<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Invitation;
use App\Filter\AssociateFilter;
use App\Form\InvitationType;
use App\Form\UserSearchType;
use App\Form\UserType;
use App\Service\InvitationManager;
use App\Entity\UpdateProfile;
use App\Entity\User;
use App\Form\UpdateProfileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AdminController extends AbstractController
{
    const ASSOCIATE_LIMIT = 20;
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/profile", name="admin_profile")
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminProfile(UserPasswordEncoderInterface $encoder, Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form['oldPassword']->getData();
            if (!$encoder->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('error', 'Old password is not correct');
            } else {
                $user->setPlainPassword($form['newPassword']->getData());

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->persist($user->getAssociate());
                $em->flush();

                $this->addFlash('success', 'Fields updated');
            }
        }

        return $this->render('admin/profile.html.twig', [
            'updateProfile' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/invite", name="admin_invite")
     * @param Request $request
     * @param InvitationManager $invitationManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminInvitation(Request $request, InvitationManager $invitationManager)
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

        return $this->render('admin/invitation.html.twig', [
            'invitation' => $form->createView()
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
     * @Route("/admin/api/associates/{id}", name="get_associate")
     */
    public function getAssociate($id)
    {
        $associateRepository = $this->getDoctrine()->getRepository(Associate::class);
        $associate = $associateRepository->find($id);
        return $this->render('admin/usersearch.html.twig', ['associate' => $associate]);
    }
    /**
     * @Route("/admin/api/associates", name="user_search_associates")
     * @param int $page
     * @param Request $request
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
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

        $serializer = new Serializer([new ObjectNormalizer()]);

        $serializedAssociates = $serializer->normalize(
            $limitedAssociates,
            null,
            ['attributes' => ['fullName', 'email', 'telephone']]
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userSearch()
    {
        $form = $this->createForm(UserSearchType::class);

        return $this->render('admin/usersearch.html.twig', [
            'usersearch' => $form->createView()
        ]);
    }
}
