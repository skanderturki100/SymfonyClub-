<?php

namespace App\Repository;

use App\Entity\Club;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Club::class);
    }

    /**
     * @return Club[]
     */
    public function findActiveClubs(): array
    {
        return $this->findByStatus('active');
    }

    /**
     * @return Club[]
     */
    public function findClubsCreatedAfter(\DateTime $date): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.dateCreationClub > :date')
            ->setParameter('date', $date)
            ->orderBy('c.dateCreationClub', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Club[]
     */
    private function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statusClubs = :status')
            ->setParameter('status', $status)
            ->orderBy('c.nomClub', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

