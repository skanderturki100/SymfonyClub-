<?php

namespace App\Entity;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début ne peut pas être vide.")]
    
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin ne peut pas être vide.")]
    
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255)]
    private ?string $motif = null;

    

    #[ORM\ManyToOne(targetEntity: Ressources::class, inversedBy: 'reservations')]
    //#[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]







    private ?Ressources $ressource = null;

    #[ORM\Column(type: 'integer')]
 
     private $quantite;


    public function getId(): ?int
    {
        return $this->id;
    }

    




    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getRessource(): ?Ressources
    {
        return $this->ressource;
    }

    public function setRessource(?Ressources $ressource): static
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getQuantite(): ?int
{
    return $this->quantite;
}

public function setQuantite(int $quantite): self
{
    $this->quantite = $quantite;

    return $this;
}


    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->dateDebut && $this->dateFin) {
            // Vérifie si la date de fin est avant la date de début
            if ($this->dateFin < $this->dateDebut) {
                $context->buildViolation('La date de fin ne peut pas être avant la date de début.')
                    ->atPath('dateFin') // Ajoute l'erreur à la date de fin
                    ->addViolation();
            }
    
            // Vérifie si la date de fin est égale à la date de début
            if ($this->dateDebut == $this->dateFin) {
                $context->buildViolation('La date de fin ne peut pas être égale à la date de début.')
                    ->atPath('dateFin') // Ajoute l'erreur à la date de fin
                    ->addViolation();
            }
        }
    }
    


}
