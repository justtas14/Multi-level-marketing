<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Associate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $associateId;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $level = 0;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $ancestors = "|";

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $parentId = -1;

    /**
     * @ORM\ManyToOne(targetEntity="Associate")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id")
     * @var Associate
     */
    private $parent;



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
     * @ORM\Column(type="string")
     * @var string
     */
    private $country = "";

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $address = "";

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $city = "";

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $postcode = "";

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $mobilePhone = "";

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $homePhone = "";

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $agreedToEmailUpdates = false;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $agreedToTextMessageUpdates = false;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $agreedToSocialMediaUpdates = false;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Associate
     */
    public function setId(int $id): Associate
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssociateId(): string
    {
        return $this->associateId;
    }

    /**
     * @param string $associateId
     * @return Associate
     */
    public function setAssociateId(string $associateId): Associate
    {
        $this->associateId = $associateId;
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
     * @return string
     */
    public function getAncestors(): string
    {
        return $this->ancestors;
    }



    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }



    /**
     * @return Associate
     */
    public function getParent(): Associate
    {
        return $this->parent;
    }

    /**
     * @param Associate $parent
     * @return Associate
     */
    public function setParent(Associate $parent): Associate
    {
        $this->parent = $parent;
        $this->parentId = $parent->getId();
        $this->ancestors = $parent->getAncestors().$parent->getId().'|';
        $this->level = $parent->getLevel() + 1;
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
     * @return Associate
     */
    public function setFullName(string $fullName): Associate
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Associate
     */
    public function setCountry(string $country): Associate
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Associate
     */
    public function setAddress(string $address): Associate
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Associate
     */
    public function setCity(string $city): Associate
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     * @return Associate
     */
    public function setPostcode(string $postcode): Associate
    {
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getMobilePhone(): string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string $mobilePhone
     * @return Associate
     */
    public function setMobilePhone(string $mobilePhone): Associate
    {
        $this->mobilePhone = $mobilePhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getHomePhone(): string
    {
        return $this->homePhone;
    }

    /**
     * @param string $homePhone
     * @return Associate
     */
    public function setHomePhone(string $homePhone): Associate
    {
        $this->homePhone = $homePhone;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAgreedToEmailUpdates(): bool
    {
        return $this->agreedToEmailUpdates;
    }

    /**
     * @param bool $agreedToEmailUpdates
     * @return Associate
     */
    public function setAgreedToEmailUpdates(bool $agreedToEmailUpdates): Associate
    {
        $this->agreedToEmailUpdates = $agreedToEmailUpdates;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAgreedToTextMessageUpdates(): bool
    {
        return $this->agreedToTextMessageUpdates;
    }

    /**
     * @param bool $agreedToTextMessageUpdates
     * @return Associate
     */
    public function setAgreedToTextMessageUpdates(bool $agreedToTextMessageUpdates): Associate
    {
        $this->agreedToTextMessageUpdates = $agreedToTextMessageUpdates;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAgreedToSocialMediaUpdates(): bool
    {
        return $this->agreedToSocialMediaUpdates;
    }

    /**
     * @param bool $agreedToSocialMediaUpdates
     * @return Associate
     */
    public function setAgreedToSocialMediaUpdates(bool $agreedToSocialMediaUpdates): Associate
    {
        $this->agreedToSocialMediaUpdates = $agreedToSocialMediaUpdates;
        return $this;
    }

    public function getAgreedToEmailUpdates(): ?bool
    {
        return $this->agreedToEmailUpdates;
    }

    public function getAgreedToTextMessageUpdates(): ?bool
    {
        return $this->agreedToTextMessageUpdates;
    }

    public function getAgreedToSocialMediaUpdates(): ?bool
    {
        return $this->agreedToSocialMediaUpdates;
    }

    public function setAncestors(string $ancestors): self
    {
        $this->ancestors = $ancestors;

        return $this;
    }

    public function setParentId(int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

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
     * @return Associate
     */
    public function setEmail(string $email): Associate
    {
        $this->email = $email;
        return $this;
    }



}