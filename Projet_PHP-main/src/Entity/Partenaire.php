<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\PartenaireRepository')]
class Partenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idPartenaire = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nomPartenaire;

    #[ORM\Column(type: 'string', length: 255)]
    private string $clubPartenaire;

    #[ORM\Column(type: 'string', length: 255)]
    private string $telPartenaire;

    #[ORM\Column(type: 'string', length: 255)]
    private string $mailPartenaire;

    #[ORM\Column(type: 'string', length: 255)]
    private string $typePartenaire;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $datePartenariat;

    #[ORM\Column(type: 'string', length: 255)]
    private string $projet;

    #[ORM\Column(type: 'string', length: 255)]
    private string $statusPartenariat;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotNull(message: 'La date de début de contrat est obligatoire.')]
    private ?\DateTimeInterface $dateDebutContrat = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotNull(message: 'La date de fin de contrat est obligatoire.')]
    #[Assert\GreaterThan(propertyPath: "dateDebutContrat", message: "La date de fin de contrat doit être après la date de début.")]
    private ?\DateTimeInterface $dateFinContrat = null;

    #[ORM\Column(type: 'boolean')]
    private bool $contratActif = true;

    // Getters et setters

    public function getIdPartenaire(): ?int
    {
        return $this->idPartenaire;
    }

    public function getNomPartenaire(): string
    {
        return $this->nomPartenaire;
    }

    public function setNomPartenaire(string $nomPartenaire): self
    {
        $this->nomPartenaire = $nomPartenaire;
        return $this;
    }

    public function getClubPartenaire(): string
    {
        return $this->clubPartenaire;
    }

    public function setClubPartenaire(string $clubPartenaire): self
    {
        $this->clubPartenaire = $clubPartenaire;
        return $this;
    }

    public function getTelPartenaire(): string
    {
        return $this->telPartenaire;
    }

    public function setTelPartenaire(string $telPartenaire): self
    {
        $this->telPartenaire = $telPartenaire;
        return $this;
    }

    public function getMailPartenaire(): string
    {
        return $this->mailPartenaire;
    }

    public function setMailPartenaire(string $mailPartenaire): self
    {
        $this->mailPartenaire = $mailPartenaire;
        return $this;
    }

    public function getTypePartenaire(): string
    {
        return $this->typePartenaire;
    }

    public function setTypePartenaire(string $typePartenaire): self
    {
        $this->typePartenaire = $typePartenaire;
        return $this;
    }

    public function getDatePartenariat(): \DateTimeInterface
    {
        return $this->datePartenariat;
    }

    public function setDatePartenariat(\DateTimeInterface $datePartenariat): self
    {
        $this->datePartenariat = $datePartenariat;
        return $this;
    }

    public function getProjet(): string
    {
        return $this->projet;
    }

    public function setProjet(string $projet): self
    {
        $this->projet = $projet;
        return $this;
    }

    public function getStatusPartenariat(): string
    {
        return $this->statusPartenariat;
    }

    public function setStatusPartenariat(string $statusPartenariat): self
    {
        $this->statusPartenariat = $statusPartenariat;
        return $this;
    }

    public function getDateDebutContrat(): ?\DateTimeInterface
    {
        return $this->dateDebutContrat;
    }

    public function setDateDebutContrat(\DateTimeInterface $dateDebutContrat): self
    {
        $this->dateDebutContrat = $dateDebutContrat;
        return $this;
    }

    public function getDateFinContrat(): ?\DateTimeInterface
    {
        return $this->dateFinContrat;
    }

    public function setDateFinContrat(\DateTimeInterface $dateFinContrat): self
    {
        $this->dateFinContrat = $dateFinContrat;
        return $this;
    }

    public function isContratActif(): bool
    {
        return $this->contratActif;
    }

    public function setContratActif(bool $contratActif): self
    {
        $this->contratActif = $contratActif;
        return $this;
    }
}
