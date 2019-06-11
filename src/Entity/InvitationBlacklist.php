<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class InvitationBlacklist
 * @ORM\Entity(repositoryClass="App\Repository\InvitationBlacklistRepository")
 * @package App\Entity
 */
class InvitationBlacklist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return InvitationBlacklist
     */
    public function setId(?int $id): InvitationBlacklist
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return InvitationBlacklist
     */
    public function setEmail(?string $email): InvitationBlacklist
    {
        $this->email = $email;
        return $this;
    }
}
