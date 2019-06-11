<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints as AssertApp;

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
     * @var File
     * @ORM\OneToOne(targetEntity="File", cascade={"persist"})
     * @ORM\JoinColumn(name="target_file_id", referencedColumnName="id")
     * @AssertApp\IsImage(message="Only images are allowed")
     */
    private $mainLogo;

    /**
     * @var File
     * @ORM\OneToOne(targetEntity="File", cascade={"persist"})
     * @ORM\JoinColumn(name="target_file_id2", referencedColumnName="id", onDelete="SET NULL")
     */
    private $termsOfServices;

    /**
     * @ORM\Column(type="text")
     */
    private $landingContent;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $hasPrelaunchEnded = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return File
     */
    public function getMainLogo(): ?File
    {
        return $this->mainLogo;
    }

    /**
     * @param File $mainLogo
     * @return Configuration
     */
    public function setMainLogo(?File $mainLogo): Configuration
    {
        $this->mainLogo = $mainLogo;

        return $this;
    }

    /**
     * @return File
     */
    public function getTermsOfServices(): ?File
    {
        return $this->termsOfServices;
    }

    /**
     * @param File|null $termsOfServices
     * @return Configuration
     */
    public function setTermsOfServices(?File $termsOfServices): Configuration
    {
        $this->termsOfServices = $termsOfServices;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLandingContent()
    {
        return $this->landingContent;
    }

    /**
     * @param mixed $landingContent
     * @return Configuration
     */
    public function setLandingContent($landingContent): Configuration
    {
        $this->landingContent = $landingContent;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPrelaunchEnded(): bool
    {
        return $this->hasPrelaunchEnded;
    }

    /**
     * @param bool $hasPrelaunchEnded
     * @return Configuration
     */
    public function setPrelaunchEnded(bool $hasPrelaunchEnded): Configuration
    {
        $this->hasPrelaunchEnded = $hasPrelaunchEnded;

        return $this;
    }
}
