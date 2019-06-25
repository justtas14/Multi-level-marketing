<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmailTemplateRepository")
 */
class EmailTemplate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $emailSubject = "";

    /**
     * @ORM\Column(type="text")
     */
    private $emailBody = "";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailType;

    /**
     * @param int $id
     * @return EmailTemplate
     */
    public function setId(?int $id): EmailTemplate
    {
        $this->id = $id;
        return $this;
    }

    public function getEmailSubject(): ?string
    {
        return $this->emailSubject;
    }

    public function setEmailSubject(string $emailSubject): self
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }

    public function getEmailBody(): ?string
    {
        return $this->emailBody;
    }

    public function setEmailBody(string $emailBody): self
    {
        $this->emailBody = $emailBody;

        return $this;
    }

    public function getEmailType(): ?string
    {
        return $this->emailType;
    }

    public function setEmailType($emailType): self
    {
        $this->emailType = $emailType;

        return $this;
    }
}
