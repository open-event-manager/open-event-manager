<?php

namespace App\Entity;

use App\Repository\KeycloakGroupsToStandortsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KeycloakGroupsToStandortsRepository::class)
 */
class KeycloakGroupsToStandorts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Standort::class, inversedBy="keycloakGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $standort;

    /**
     * @ORM\Column(type="text")
     */
    private $keycloakGroup;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStandort(): ?Standort
    {
        return $this->standort;
    }

    public function setStandort(?Standort $standort): self
    {
        $this->standort = $standort;

        return $this;
    }

    public function getKeycloakGroup(): ?string
    {
        return $this->keycloakGroup;
    }

    public function setKeycloakGroup(string $keycloakGroup): self
    {
        $this->keycloakGroup = $keycloakGroup;

        return $this;
    }
}
