<?php

namespace App\CustomNormalizer;

use App\Entity\Configuration;
use App\Entity\Gallery;
use App\Service\ConfigurationManager;
use Doctrine\ORM\EntityManagerInterface;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ConfigurationNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;
    private $gaufretteFileManager;
    private $entityManager;
    private $cm;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        GaufretteFileManager $gaufretteFileManager,
        EntityManagerInterface $entityManager,
        ConfigurationManager $cm
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->gaufretteFileManager = $gaufretteFileManager;
        $this->entityManager = $entityManager;
        $this->cm = $cm;
    }

    public function normalize($entity, $format = null, array $context = [])
    {
        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $this->normalizer]);
        $this->normalizer->setSerializer($serializer);
        $data = $this->normalizer->normalize($entity, $format, $context);

        if ($entity->getMainLogo()) {
            $data['mainLogo']['filePath'] = $this->gaufretteFileManager->generateDownloadUrl($entity->getMainLogo());
            $data['mainLogo']['id'] = $this->entityManager->getRepository(Gallery::class)->findBy(
                ['galleryFile' => $entity->getMainLogo()]
            )['0']->getId();
            $data['mainLogo']['fileName'] = $entity->getMainLogo()->getOriginalName();
        }
        if ($entity->getTermsOfServices()) {
            $data['termsOfServices']['filePath'] = $this->gaufretteFileManager->
                generateDownloadUrl($entity->getTermsOfServices());
            $data['termsOfServices']['id'] = $this->entityManager->getRepository(Gallery::class)->findBy(
                ['galleryFile' => $entity->getTermsOfServices()]
            )['0']->getId();
            $data['termsOfServices']['fileName'] = $entity->getTermsOfServices()->getOriginalName();
        }

        $data['hasPrelaunchEnded'] = $entity->hasPrelaunchEnded();
        $data['landingContent'] = $this->cm->getParsedLandingContent($entity->getLandingContent());

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Configuration;
    }
}
