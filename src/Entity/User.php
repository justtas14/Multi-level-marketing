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
    public function getAssociate(): Associate
    {
        return $this->associate;
    }

    /**
     * @param Associate $associate
     * @return User
     */
    public function setAssociate(Associate $associate): User
    {
        $this->associate = $associate;
        return $this;
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}
