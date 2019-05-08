<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Invitation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $fullName = "";

    /**
     * @ORM\ManyToOne(targetEntity="Associate")
     * @var Associate
     */
    private $sender;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $used = false;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Invitation
     */
    public function setId(int $id): Invitation
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return Invitation
     */
    public function setFullName(string $fullName): Invitation
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return Associate
     */
    public function getSender(): Associate
    {
        return $this->sender;
    }

    /**
     * @param Associate $sender
     * @return Invitation
     */
    public function setSender(Associate $sender): Invitation
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * @param bool $used
     * @return Invitation
     */
    public function setUsed(bool $used): Invitation
    {
        $this->used = $used;
        return $this;
    }

    public function getUsed(): ?bool
    {
        return $this->used;
    }
}