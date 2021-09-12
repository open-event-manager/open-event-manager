<?php

namespace App\Entity;

use App\Repository\FreeFieldRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FreeFieldRepository::class)
 */
class FreeField
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $label;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $required;

    /**
     * @ORM\ManyToOne(targetEntity=Rooms::class, inversedBy="freeFields",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Room;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getRoom(): ?Rooms
    {
        return $this->Room;
    }

    public function setRoom(?Rooms $Room): self
    {
        $this->Room = $Room;

        return $this;
    }
}
