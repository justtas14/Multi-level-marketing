<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigurationRepository")
 */
class Configuration
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Image(
     *     minWidth = 100,
     *     maxWidth = 200,
     *     minHeight = 100,
     *     maxHeight = 200
     * )
     */
    private $mainLogo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrelaunchEnded = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMainLogo() : File
    {
        return $this->mainLogo;
    }

    public function setMainLogo(File $file = null): self
    {
        $this->mainLogo = $file;

        return $this;
    }

    public function getIsPrelaunchEnded(): ?bool
    {
        return $this->isPrelaunchEnded;
    }

    public function setIsPrelaunchEnded(bool $isPrelaunchEnded): self
    {
        $this->isPrelaunchEnded = $isPrelaunchEnded;

        return $this;
    }
}
