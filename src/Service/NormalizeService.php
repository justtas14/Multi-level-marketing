<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Serializer\Serializer;

class NormalizeService
{
    private $normalizers;

    private $attributes;

    private $entity;

    public function __construct($entity, $normalizers = [], $attributes = [])
    {
        $this->normalizers = $normalizers;
        $this->attributes = $attributes;
        $this->entity = $entity;
    }

    public function normalizeObject()
    {
        $serializer = new Serializer($this->normalizers);
        return $serializer->normalize(
            $this->entity,
            null,
            ['attributes' => $this->attributes]
        );
    }
}
