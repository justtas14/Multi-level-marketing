<?php

namespace App\Controller;

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

class AdminController extends AbstractController
{




}
