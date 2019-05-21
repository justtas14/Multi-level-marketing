<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity
 */
class Invitation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $invitationCode = "";

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email = "";

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
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $created;

    public function __construct()
    {
        $this->setInvitationCode(md5(time()));
        $this->setCreated(time());
    }

    /**
     * @return Integer
     */
    public function getId(): Integer
    {
        return $this->id;
    }

    /**
     * @param Integer $id
     * @return Invitation
     */
    public function setId(Integer $id): Invitation
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Invitation
     */
    public function setEmail(string $email): Invitation
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvitationCode(): string
    {
        return $this->invitationCode;
    }

    /**
     * @param string $code
     * @return Invitation
     */
    public function setInvitationCode(string $code): Invitation
    {
        $this->invitationCode = $code;
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

    /**
     * @return mixed
     */
    public function getCreated() : int
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return Invitation
     */
    public function setCreated(int $created): Invitation
    {
        $this->created = $created;
        return $this;
    }
}