<?php

namespace App\CustomNormalizer;

use App\Entity\Configuration;
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

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        GaufretteFileManager $gaufretteFileManager
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->gaufretteFileManager = $gaufretteFileManager;
    }

    public function normalize($entity, $format = null, array $context = [])
    {
        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $this->normalizer]);
        $this->normalizer->setSerializer($serializer);
        $data = $this->normalizer->normalize($entity, $format, $context);

        if ($entity->getMainLogo()) {
            $data['mainLogoPath'] = $this->gaufretteFileManager->generateDownloadUrl($entity->getMainLogo());
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Configuration;
    }
}
