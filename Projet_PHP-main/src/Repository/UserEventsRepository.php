<?php
// src/Repository/EventRepository.php
namespace App\Repository;

use App\Entity\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class UserEventsRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    // Méthode de recherche avec tri
    public function findBySearch($search = '', $sort = 'id', $order = 'ASC')
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e')
                     ->from(Events::class, 'e');

        if ($search) {
            $queryBuilder->where('e.nomEvent LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        $queryBuilder->orderBy('e.' . $sort, $order);

        return $queryBuilder->getQuery()->getResult();
    }

    // Méthode pour obtenir un événement par son ID
    public function find($id)
    {
        return $this->entityManager->getRepository(Events::class)->find($id);
    }
}
