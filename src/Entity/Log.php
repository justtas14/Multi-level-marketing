<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 */
class Log
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    public function __construct()
    {
        $this->setCreated(new \DateTime());
    }

    /**
     * @param int $id
     * @return Log
     */
    public function setId(?int $id): Log
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
    public function getLogMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Log
     */
    public function setLogMessage(string $message): Log
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param \DateTime $time
     * @return Log
     */
    public function setCreated(\DateTime $time): Log
    {
        $this->createdAt = $time;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreated(): ?\DateTime
    {
        return $this->createdAt;
    }
}
