<?php

namespace App\Repository;

use App\Entity\FreeField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FreeField|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreeField|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreeField[]    findAll()
 * @method FreeField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreeFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreeField::class);
    }

    // /**
    //  * @return FreeField[] Returns an array of FreeField objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FreeField
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
