<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
#[Vich\Uploadable]

class Club
{
   #[Vich\UploadableField(mapping: 'club_photo', fileNameProperty: 'photo')]
   private ?File $photoFile = null;

   #[ORM\Column(type: 'string', length: 255, nullable: true)]
   private ?string $photo = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idClub = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nomClub = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $categorieClub = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateCreationClub = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $adresseClub = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    #[Assert\Regex(
        pattern: "/^\+?[0-9]{1,4}?[-. ]?(\(?\d{1,3}?\)?[-. ]?\d{1,4}[-. ]?\d{1,4}[-. ]?\d{1,9})$/",
        message: "Invalid phone number."
    )]
    private ?string $telClub = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $siteWeb = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $statusClubs = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $attribute5 = null;

    // Getter and Setter methods

    
   

    public function getIdClub(): ?int
    {
        return $this->idClub;
    }

    public function getNomClub(): ?string
    {
        return $this->nomClub;
    }

    public function setNomClub(?string $nomClub): self
    {
        $this->nomClub = $nomClub;
        return $this;
    }

    public function getCategorieClub(): ?string
    {
        return $this->categorieClub;
    }

    public function setCategorieClub(?string $categorieClub): self
    {
        $this->categorieClub = $categorieClub;
        return $this;
    }

    public function getDateCreationClub(): ?\DateTimeInterface
    {
        return $this->dateCreationClub;
    }

    public function setDateCreationClub(\DateTimeInterface $dateCreationClub): self
    {
        $this->dateCreationClub = $dateCreationClub;
        return $this;
    }

    public function getAdresseClub(): ?string
    {
        return $this->adresseClub;
    }

    public function setAdresseClub(?string $adresseClub): self
    {
        $this->adresseClub = $adresseClub;
        return $this;
    }

    public function getTelClub(): ?string
    {
        return $this->telClub;
    }

    public function setTelClub(?string $telClub): self
    {
        $this->telClub = $telClub;
        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(?string $siteWeb): self
    {
        $this->siteWeb = $siteWeb;
        return $this;
    }

    public function getStatusClubs(): ?string
    {
        return $this->statusClubs;
    }

    public function setStatusClubs(?string $statusClubs): self
    {
        $this->statusClubs = $statusClubs;
        return $this;
    }

    public function getAttribute5(): ?string
    {
        return $this->attribute5;
    }

    public function setAttribute5(?string $attribute5): self
    {
        $this->attribute5 = $attribute5;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }
 
    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }
 
    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;
 
        if ($photoFile) {
            // If the file is set, we can update the updatedAt timestamp to trigger VichUploader
            $this->dateCreationClub = new \DateTimeImmutable();
        }
 
    }
}
