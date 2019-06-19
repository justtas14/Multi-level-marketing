<?php


namespace App\Entity;

use PlumTreeSystems\UserBundle\Entity\User as PTSUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class User extends PTSUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Associate")
     * @var Associate
     * @Assert\Valid
     */
    private $associate;

    /**
     * @ORM\Column(type="string", length=191, unique=true, nullable=true)
     * @var string
     */
    private $resetPasswordCode = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastResetAt = null;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Associate
     */
    public function getAssociate(): ?Associate
    {
        return $this->associate;
    }

    /**
     * @param Associate $associate
     * @return User
     */
    public function setAssociate(?Associate $associate): User
    {
        $this->associate = $associate;
        return $this;
    }

    /**
     * @return string
     */
    public function getResetPasswordCode(): ?string
    {
        return $this->resetPasswordCode;
    }

    /**
     * @param string $code
     * @return User
     */
    public function setResetPasswordCode(?string $code): self
    {
        $this->resetPasswordCode = $code;
        return $this;
    }

    /**
     * @param \DateTime $time
     * @return User
     */
    public function setLastResetAt(\DateTime $time): self
    {
        $this->lastResetAt = $time;
        return $this;
    }

    public function getLastResetAt(): ?\DateTime
    {
        return $this->lastResetAt;
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}
