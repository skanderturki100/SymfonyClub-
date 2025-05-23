<?php

namespace App\Entity;

use App\Repository\FeedBacksRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: FeedBacksRepository::class)]
class FeedBacks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idFeedBack = null;

    #[ORM\ManyToOne(targetEntity: Club::class, fetch: "EAGER")] // Specify fetch type
    #[ORM\JoinColumn(name: "id_club", referencedColumnName: "id_club", nullable: false)]
    private Club $idClub;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user", nullable: true)] // Fixed "is_user" to "id_user"
    private ?User $idUser = null;

    #[ORM\Column(type: "string", length: 255)] // Add max length for string
    private string $statusFeedback;

    #[ORM\Column(type: "datetime")]
    private DateTimeInterface $dateFeedback;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: "integer", nullable: false, options: ["default" => 0])] // Default value for 'avis'
    private int $avis = 0;

    // Getters and Setters
    public function getIdFeedBack(): ?int
    {
        return $this->idFeedBack;
    }

    public function getIdClub(): Club
    {
        return $this->idClub;
    }

    public function setIdClub(Club $idClub): self
    {
        $this->idClub = $idClub;
        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function getStatusFeedback(): string
    {
        return $this->statusFeedback;
    }

    public function setStatusFeedback(string $statusFeedback): self
    {
        $this->statusFeedback = $statusFeedback;
        return $this;
    }

    public function getDateFeedback(): DateTimeInterface
    {
        return $this->dateFeedback;
    }

    public function setDateFeedback(DateTimeInterface $dateFeedback): self
    {
        $this->dateFeedback = $dateFeedback;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getAvis(): int
    {
        return $this->avis;
    }

    public function setAvis(int $avis): self
    {
        $this->avis = $avis;
        return $this;
    }
}
