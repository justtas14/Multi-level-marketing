<?php


namespace App\Filter;

class AssociateFilter
{
    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $telephone;

    /**
     * @return string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return AssociateFilter
     */
    public function setFullName(?string $fullName): AssociateFilter
    {
        $this->fullName = $fullName;
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
     * @return AssociateFilter
     */
    public function setEmail(?string $email): AssociateFilter
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     * @return AssociateFilter
     */
    public function setTelephone(?string $telephone): AssociateFilter
    {
        $this->telephone = $telephone;
        return $this;
    }
}
