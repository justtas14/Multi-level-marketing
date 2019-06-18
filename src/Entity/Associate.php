<?php


namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AssertApp;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssociateRepository")
 */
class Associate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="guid", unique=true)
     * @var string
     */
    private $associateId;

    /**
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
     * @Assert\NotBlank
     * @Assert\Email(
     *   message = "The email '{{ value }}' is not a valid email."
     * )
     * @ORM\Column(type="string")
     * @var string
     */
    private $email = "";

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @var string
     */
    private $fullName = "";

    /**
     * @var File
     * @ORM\OneToOne(targetEntity="File", cascade={"persist"})
     * @ORM\JoinColumn(name="target_file_id", referencedColumnName="id", onDelete="SET NULL")
     * @AssertApp\IsImage(message="Only images are allowed")
     */
    private $profilePicture;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @var string
     */
    private $country = "";

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @var string
     */
    private $address = "";

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $address2 = "";

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @var string
     */
    private $city = "";

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @var string
     */
    private $postcode = "";

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @var string
     */
    private $mobilePhone = "";

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $homePhone = "";

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $agreedToEmailUpdates = false;

    /**
     * @ORM\Column(type="boolean"))
     * @var boolean
     */
    private $agreedToTextMessageUpdates = false;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $agreedToSocialMediaUpdates = false;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $joinDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(groups={"registration"}, message="Date of birth is required")
     * @var DateTime
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     * @Assert\IsTrue
     */
    private $agreedToTermsOfService = false;

    public function __construct()
    {
        $this->setAssociateId(Uuid::uuid4());
        $this->joinDate = new DateTime();
    }

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
     * @return File
     */
    public function getProfilePicture(): ?File
    {
        return $this->profilePicture;
    }

    /**
     * @param File|null $profilePicture
     * @return Associate
     */
    public function setProfilePicture(?File $profilePicture): Associate
    {
        $this->profilePicture = $profilePicture;

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
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     * @return Associate
     */
    public function setAddress2(?string $address2): Associate
    {
        if ($address2) {
            $this->address2 = $address2;
        }
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
    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    /**
     * @param string $homePhone
     * @return Associate
     */
    public function setHomePhone(?string $homePhone): Associate
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

    /**
     * @return DateTime
     */
    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @param DateTime $dateOfBirth
     * @return Associate
     */
    public function setDateOfBirth(?DateTime $dateOfBirth): Associate
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAgreedToTermsOfService(): ?bool
    {
        return $this->agreedToTermsOfService;
    }

    /**
     * @param bool $agreedToTermsOfService
     * @return Associate
     */
    public function setAgreedToTermsOfService(?bool $agreedToTermsOfService): Associate
    {
        $this->agreedToTermsOfService = $agreedToTermsOfService;
        return $this;
    }

    public function toArray()
    {
        $displayCountry = '';
        if ($this->country && strlen($this->country) === 2) {
            $displayCountry = Intl::getRegionBundle()->getCountryName($this->country);
        }
        return [
            'id' => $this->getId(),
            'sponsorId' => $this->getParentId(),
            'level' => $this->getLevel(),
            'fullName' => $this->getFullName(),
            'email' => $this->getEmail(),
            'country' => $displayCountry,
            'address' => $this->getAddress(),
            'address2' => $this->getAddress2(),
            'city' => $this->getCity(),
            'postcode' => $this->getPostcode(),
            'mobilePhone' => $this->getMobilePhone(),
            'homePhone' => $this->getHomePhone(),
            'agreedToEmailUpdates' => $this->getAgreedToEmailUpdates()?'Yes':'No',
            'agreedToTextMessageUpdates' => $this->getAgreedToTextMessageUpdates()?'Yes':'No',
            'agreedToSocialMediaUpdates' => $this->getAgreedToSocialMediaUpdates()?'Yes':'No',
            'agreedToTermsOfService' => $this->getAgreedToTermsOfService()?'Yes':'No',
            'joinDate' => $this->getJoinDate()->format('Y-m-d'),
            'dateOfBirth' => ($this->getDateOfBirth() instanceof DateTime)
                ? $this->getDateOfBirth()->format('Y-m-d')
                : '-'
        ];
    }

    /**
     * @return DateTime
     */
    public function getJoinDate(): DateTime
    {
        return $this->joinDate;
    }
}
