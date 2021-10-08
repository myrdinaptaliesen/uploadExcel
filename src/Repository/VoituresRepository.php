<?php

namespace App\Repository;

use App\Entity\Voitures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Voitures|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voitures|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voitures[]    findAll()
 * @method Voitures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoituresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voitures::class);
    }

    // /**
    //  * @return Voitures[] Returns an array of Voitures objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Voitures
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
