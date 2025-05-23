<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: EventsRepository::class)]
class Events
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column ]
    private ?int $id = null;

    
    #[ORM\Column(length: 255)]
    private ?string $nomEvent = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $debutEvent = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $finEvent = null;

    #[ORM\Column(length: 255)]
    private ?string $typeEvent = null;

    #[ORM\Column]
    private ?int $idClub = null;

    #[ORM\Column(type: Types::JSON)]
    private array $membreInscrits = [];

    #[ORM\Column(type: Types::JSON)]
    private array $ressources = [];

    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $descriptionEvent = null;
    
   

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $photo;

    

    
   

public function getPhoto(): ?string
{
    return $this->photo;
}

public function setPhoto(?string $photo): self
{
    $this->photo = $photo;

    return $this;
}


    public function getId(): ?int
    {
        return $this->id;
    }

    

    

    public function getNomEvent(): ?string
    {
        return $this->nomEvent;
    }

    public function setNomEvent(string $nomEvent): static
    {
        $this->nomEvent = $nomEvent;

        return $this;
    }

    public function getDebutEvent(): ?\DateTimeInterface
    {
        return $this->debutEvent;
    }

    public function setDebutEvent(\DateTimeInterface $debutEvent): static
    {
        $this->debutEvent = $debutEvent;

        return $this;
    }

    public function getFinEvent(): ?\DateTimeInterface
    {
        return $this->finEvent;
    }

    public function setFinEvent(\DateTimeInterface $finEvent): static
    {
        $this->finEvent = $finEvent;

        return $this;
    }

    public function getTypeEvent(): ?string
    {
        return $this->typeEvent;
    }

    public function setTypeEvent(string $typeEvent): static
    {
        $this->typeEvent = $typeEvent;

        return $this;
    }

    public function getIdClub(): ?int
    {
        return $this->idClub;
    }

    public function setIdClub(int $idClub): static
    {
        $this->idClub = $idClub;

        return $this;
    }

    public function getMembreInscrits(): array
    {
        return $this->membreInscrits;
    }

    public function setMembreInscrits(array $membreInscrits): static
    {
        $this->membreInscrits = $membreInscrits;

        return $this;
    }

    public function getRessources(): array
    {
        return $this->ressources;
    }

    public function setRessources(array $ressources): static
    {
        $this->ressources = $ressources;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDescriptionEvent(): ?string
    {
        return $this->descriptionEvent;
    }

    public function setDescriptionEvent(string $descriptionEvent): static
    {
        $this->descriptionEvent = $descriptionEvent;

        return $this;
    }

    
}
