<?php

namespace App\Repository;

use App\Entity\Innovation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserInnovationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Innovation::class);
    }

    /**
     * Recherche les innovations par titre.
     *
     * @param string $titre
     * @return Innovation[]
     */
    public function findByTitre(string $titre): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.titre LIKE :titre')
            ->setParameter('titre', '%' . $titre . '%')
            ->orderBy('i.dateCreationInnovation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
