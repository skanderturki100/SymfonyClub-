<?php

namespace App\Repository;

use App\Entity\Partenaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Partenaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partenaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partenaire[]    findAll()
 * @method Partenaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartenaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partenaire::class);
    }

    /**
     * Recherche les partenaires par type
     */
    public function findByType($type)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.typePartenaire = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les partenaires dont le contrat commence à partir d'une date donnée
     */
    public function findByDateDebutContrat($dateDebut)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateDebutContrat >= :dateDebut')
            ->setParameter('dateDebut', $dateDebut)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les partenaires dont le contrat se termine avant une certaine date
     */
    public function findByDateFinContrat($dateFin)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateFinContrat <= :dateFin')
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les partenaires dont le contrat est encore actif
     */
    public function findActiveContracts()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.contratActif = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les partenaires dont le contrat expire dans les N prochains jours
     */
    public function findExpiringContractsInDays($days)
    {
        $dateLimit = new \DateTime();
        $dateLimit->modify("+$days days");

        return $this->createQueryBuilder('p')
            ->andWhere('p.dateFinContrat <= :dateLimit')
            ->setParameter('dateLimit', $dateLimit)
            ->getQuery()
            ->getResult();
    }
}
