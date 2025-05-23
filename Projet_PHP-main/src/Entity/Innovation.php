<?php

namespace App\Entity;

use App\Repository\InnovationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InnovationRepository::class)]
class Innovation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idMembre = null;

    #[ORM\Column(length: 255)]
    private ?string $nomClub = null;

    #[ORM\Column(length: 255)]
    private ?string $descriptionInnovation = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreationInnovation = null;

    #[ORM\Column(length: 255)]
    private ?string $statusInnovation = null;


    
    #[ORM\ManyToOne(targetEntity: Events::class, fetch: "EAGER")]
#[ORM\JoinColumn(nullable: true)]
private ?Events $events = null;


public function getEvents(): ?Events
{
    return $this->events;
}

public function setEvents(?Events $events): static
{
    $this->events = $events;
    return $this;
}


   
  




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMembre(): ?int
    {
        return $this->idMembre;
    }

    public function setIdMembre(int $idMembre): static
    {
        $this->idMembre = $idMembre;

        return $this;
    }

    public function getNomClub(): ?string
    {
        return $this->nomClub;
    }

    public function setNomClub(string $nomClub): static
    {
        $this->nomClub = $nomClub;

        return $this;
    }

    public function getDescriptionInnovation(): ?string
    {
        return $this->descriptionInnovation;
    }

    public function setDescriptionInnovation(string $descriptionInnovation): static
    {
        $this->descriptionInnovation = $descriptionInnovation;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateCreationInnovation(): ?\DateTimeInterface
    {
        return $this->dateCreationInnovation;
    }

    public function setDateCreationInnovation(\DateTimeInterface $dateCreationInnovation): static
    {
        $this->dateCreationInnovation = $dateCreationInnovation;

        return $this;
    }

    public function getStatusInnovation(): ?string
    {
        return $this->statusInnovation;
    }

    public function setStatusInnovation(string $statusInnovation): static
    {
        $this->statusInnovation = $statusInnovation;

        return $this;
    }

  
}
