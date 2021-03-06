<?php

namespace App\CustomNormalizer;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AssociateNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;
    private $gaufretteFileManager;
    private $entityManager;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        GaufretteFileManager $gaufretteFileManager,
        EntityManagerInterface $entityManager
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->gaufretteFileManager = $gaufretteFileManager;
        $this->entityManager = $entityManager;
    }

    public function normalize($entity, $format = null, array $context = [])
    {
        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $this->normalizer]);
        $this->normalizer->setSerializer($serializer);
        $data = $this->normalizer->normalize($entity, $format, $context);

        if ($entity->getProfilePicture()) {
            $data['filePath'] = $this->gaufretteFileManager->generateDownloadUrl($entity->getProfilePicture());
        }
        $associateUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $entity->getEmail()]);

        $data['roles'] = $associateUser->getRoles();

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Associate;
    }
}
