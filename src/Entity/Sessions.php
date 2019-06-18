<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Sessions
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=128)
     */
    private $sess_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sess_lifetime;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $sess_time;

    /**
     * @ORM\Column(type="blob")
     */
    private $sess_data;

    public function getSessId(): ?int
    {
        return $this->sess_id;
    }

    public function getSessLifetime(): ?int
    {
        return $this->sess_lifetime;
    }

    public function setSessLifetime(int $sess_lifetime): self
    {
        $this->sess_lifetime = $sess_lifetime;

        return $this;
    }

    public function getSessTime(): ?int
    {
        return $this->sess_time;
    }

    public function setSessTime(int $sess_time): self
    {
        $this->sess_time = $sess_time;

        return $this;
    }

    public function getSessData()
    {
        return $this->sess_data;
    }

    public function setSessData($sess_data): self
    {
        $this->sess_data = $sess_data;

        return $this;
    }
}
