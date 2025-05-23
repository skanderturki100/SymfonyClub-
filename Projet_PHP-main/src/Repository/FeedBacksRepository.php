<?php

namespace App\Repository;

use App\Entity\FeedBacks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FeedBacksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedbacks::class);
    }

    public function findFeedbacksByClub(int $clubId)
    {
        return $this->findByField('idClub', $clubId);
    }

    public function findFeedbacksByStatus(string $status)
    {
        return $this->findByField('statusFeedback', $status);
    }

    public function findFeedbacksByPerson(int $personId)
    {
        return $this->findByField('idPersonne', $personId);
    }

    private function findByField(string $field, $value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere("f.$field = :value")
            ->setParameter('value', $value)
            ->orderBy('f.dateFeedback', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

