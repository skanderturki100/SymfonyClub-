<?php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

/**
 * @extends ServiceEntityRepository<Events>
 */
class EventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Events::class);
    }
    // src/Repository/EventRepository.php

   // src/Repository/EventsRepository.php

public function findAllSorted(string $sortField = 'id', string $sortOrder = 'ASC')
{
    $qb = $this->createQueryBuilder('e');
    $qb->orderBy('e.' . $sortField, $sortOrder);

    return $qb->getQuery()->getResult();
}

public function findByNameAndSorted(string $search, string $sortField = 'id', string $sortOrder = 'ASC')
{
    $qb = $this->createQueryBuilder('e');
    $qb->where('e.nomEvent LIKE :search')
       ->setParameter('search', '%' . $search . '%')
       ->orderBy('e.' . $sortField, $sortOrder);

    return $qb->getQuery()->getResult();
}

    



    //    /**
    //     * @return Events[] Returns an array of Events objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Events
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
