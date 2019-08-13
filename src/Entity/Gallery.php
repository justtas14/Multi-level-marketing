<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */
class Gallery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @var File
     * @ORM\OneToOne(targetEntity="File", cascade={"persist"})
     * @ORM\JoinColumn(name="target_file_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $galleryFile;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $mimeType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    public function __construct()
    {
        $this->setCreated(new \DateTime());
    }

    /**
     * @param int $id
     * @return Gallery
     */
    public function setId(?int $id): Gallery
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return File
     */
    public function getGalleryFile(): ?File
    {
        return $this->galleryFile;
    }

    /**
     * @param File|null $file
     * @return Gallery
     */
    public function setGalleryFile(?File $file): Gallery
    {
        $this->galleryFile = $file;

        if ($this->getGalleryFile() && $this->getGalleryFile()->getUploadedFileReference()) {
            $this->setMimeType($this->getGalleryFile()->getUploadedFileReference()->getClientMimeType());
        }

        return $this;
    }

    /**
     * @param \DateTime $time
     * @return Gallery
     */
    public function setCreated(\DateTime $time): Gallery
    {
        $this->created = $time;
        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return Gallery
     */
    public function setMimeType(string $mimeType): Gallery
    {
        $this->mimeType = $mimeType;
        return $this;
    }
}
