<?php

namespace App\Entity;

use App\Repository\StandortRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StandortRepository::class)
 */
class Standort
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="servers")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Rooms::class, mappedBy="server")
     */
    private $rooms;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="serverAdmins")
     * @ORM\JoinColumn(nullable=false)
     */
    private $administrator;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $logoUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $smtpHost;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $smtpPort;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $smtpPassword;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $smtpUsername;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $smtpEncryption;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $smtpEmail;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $smtpSenderName;

    /**
     * @ORM\Column(type="text")
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $privacyPolicy;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $licenseKey;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $apiKey;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $staticBackgroundColor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showStaticBackgroundColor;



    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $serverEmailHeader;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $serverEmailBody;

    /**
     * @ORM\OneToMany(targetEntity=KeycloakGroupsToStandorts::class, mappedBy="server")
     */
    private $keycloakGroups;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $roomnumber;

    /**
     * @ORM\Column(type="text")
     */
    private $plz;

    /**
     * @ORM\Column(type="text")
     */
    private $city;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $directions;


    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->keycloakGroups = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection|Rooms[]
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Rooms $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms[] = $room;
            $room->setStandort($this);
        }

        return $this;
    }

    public function removeRoom(Rooms $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getStandort() === $this) {
                $room->setStandort(null);
            }
        }

        return $this;
    }

    public function getAdministrator(): ?User
    {
        return $this->administrator;
    }

    public function setAdministrator(?User $administrator): self
    {
        $this->administrator = $administrator;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getSmtpHost(): ?string
    {
        return $this->smtpHost;
    }

    public function setSmtpHost(?string $smtpHost): self
    {
        $this->smtpHost = $smtpHost;

        return $this;
    }

    public function getSmtpPort(): ?int
    {
        return $this->smtpPort;
    }

    public function setSmtpPort(?int $smtpPort): self
    {
        $this->smtpPort = $smtpPort;

        return $this;
    }

    public function getSmtpPassword(): ?string
    {
        return $this->smtpPassword;
    }

    public function setSmtpPassword(?string $smtpPassword): self
    {
        $this->smtpPassword = $smtpPassword;

        return $this;
    }

    public function getSmtpUsername(): ?string
    {
        return $this->smtpUsername;
    }

    public function setSmtpUsername(?string $smtpUsername): self
    {
        $this->smtpUsername = $smtpUsername;

        return $this;
    }

    public function getSmtpEncryption(): ?string
    {
        return $this->smtpEncryption;
    }

    public function setSmtpEncryption(?string $smtpEncryption): self
    {
        $this->smtpEncryption = $smtpEncryption;

        return $this;
    }

    public function getSmtpEmail(): ?string
    {
        return $this->smtpEmail;
    }

    public function setSmtpEmail(?string $smtpEmail): self
    {
        $this->smtpEmail = $smtpEmail;

        return $this;
    }

    public function getSmtpSenderName(): ?string
    {
        return $this->smtpSenderName;
    }

    public function setSmtpSenderName(?string $smtpSenderName): self
    {
        $this->smtpSenderName = $smtpSenderName;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrivacyPolicy(): ?string
    {
        return $this->privacyPolicy;
    }

    public function setPrivacyPolicy(?string $privacyPolicy): self
    {
        $this->privacyPolicy = $privacyPolicy;

        return $this;
    }

    public function getLicenseKey(): ?string
    {
        return $this->licenseKey;
    }

    public function setLicenseKey(?string $licenseKey): self
    {
        $this->licenseKey = $licenseKey;

        return $this;
    }

    public function getStaticBackgroundColor(): ?string
    {
        return $this->staticBackgroundColor;
    }

    public function setStaticBackgroundColor(?string $staticBackgroundColor): self
    {
        $this->staticBackgroundColor = $staticBackgroundColor;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getShowStaticBackgroundColor(): ?bool
    {
        return $this->showStaticBackgroundColor;
    }

    public function setShowStaticBackgroundColor(?bool $showStaticBackgroundColor): self
    {
        $this->showStaticBackgroundColor = $showStaticBackgroundColor;

        return $this;
    }


    public function getServerEmailHeader(): ?string
    {
        return $this->serverEmailHeader;
    }

    public function setServerEmailHeader(?string $serverEmailHeader): self
    {
        $this->serverEmailHeader = $serverEmailHeader;

        return $this;
    }

    public function getServerEmailBody(): ?string
    {
        return $this->serverEmailBody;
    }

    public function setServerEmailBody(?string $serverEmailBody): self
    {
        $this->serverEmailBody = $serverEmailBody;

        return $this;
    }

    /**
     * @return Collection|KeycloakGroupsToStandorts[]
     */
    public function getKeycloakGroups(): Collection
    {
        return $this->keycloakGroups;
    }

    public function addKeycloakGroup(KeycloakGroupsToStandorts $keycloakGroup): self
    {
        if (!$this->keycloakGroups->contains($keycloakGroup)) {
            $this->keycloakGroups[] = $keycloakGroup;
            $keycloakGroup->setStandort($this);
        }

        return $this;
    }

    public function removeKeycloakGroup(KeycloakGroupsToStandorts $keycloakGroup): self
    {
        if ($this->keycloakGroups->removeElement($keycloakGroup)) {
            // set the owning side to null (unless already changed)
            if ($keycloakGroup->getStandort() === $this) {
                $keycloakGroup->setStandort(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getRoomnumber(): ?string
    {
        return $this->roomnumber;
    }

    public function setRoomnumber(?string $roomnumber): self
    {
        $this->roomnumber = $roomnumber;

        return $this;
    }

    public function getPlz(): ?string
    {
        return $this->plz;
    }

    public function setPlz(string $plz): self
    {
        $this->plz = $plz;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDirections(): ?string
    {
        return $this->directions;
    }

    public function setDirections(?string $directions): self
    {
        $this->directions = $directions;

        return $this;
    }

}

