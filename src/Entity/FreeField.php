<?php

namespace App\Entity;

use App\Repository\FreeFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=FreeFieldsUserAnswer::class, mappedBy="freeField")
     */
    private $yes;

    public function __construct()
    {
        $this->yes = new ArrayCollection();
    }

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

    /**
     * @return Collection|FreeFieldsUserAnswer[]
     */
    public function getYes(): Collection
    {
        return $this->yes;
    }

    public function addYe(FreeFieldsUserAnswer $ye): self
    {
        if (!$this->yes->contains($ye)) {
            $this->yes[] = $ye;
            $ye->setFreeField($this);
        }

        return $this;
    }

    public function removeYe(FreeFieldsUserAnswer $ye): self
    {
        if ($this->yes->removeElement($ye)) {
            // set the owning side to null (unless already changed)
            if ($ye->getFreeField() === $this) {
                $ye->setFreeField(null);
            }
        }

        return $this;
    }
}
