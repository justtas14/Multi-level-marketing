<?php


namespace App\Entity;

use PlumTreeSystems\UserBundle\Entity\User as PTSUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class User extends PTSUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Associate")
     * @var Associate
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




}