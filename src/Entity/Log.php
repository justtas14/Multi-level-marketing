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
     * @ORM\Column(name="context", type="array")
     */
    private $context;

    /**
     * @ORM\Column(name="level", type="smallint")
     */
    private $level;

    /**
     * @ORM\Column(name="level_name", type="string", length=50)
     */
    private $levelName;

    /**
     * @ORM\Column(name="extra", type="array")
     */
    private $extra;

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
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     * @return Log
     */
    public function setContext(array $context): Log
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return Log
     */
    public function setLevel(int $level): Log
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return string
     */
    public function getLevelName(): string
    {
        return $this->levelName;
    }

    /**
     * @param string $levelName
     * @return Log
     */
    public function setLevelName(string $levelName): Log
    {
        $this->levelName = $levelName;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     * @return Log
     */
    public function setExtra(array $extra): Log
    {
        $this->extra = $extra;

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
