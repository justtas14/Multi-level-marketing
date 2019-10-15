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

class AssociateController extends AbstractController
{

    /**
     * @Route("/associate/info", name="get_associate_broken")
     */
    public function getBrokenAssociate()
    {
        return new JsonResponse("");
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
}
