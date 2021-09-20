<?php

namespace App\Repository;

use App\Entity\FreeFieldsUserAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FreeFieldsUserAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreeFieldsUserAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreeFieldsUserAnswer[]    findAll()
 * @method FreeFieldsUserAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreeFieldsUserAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreeFieldsUserAnswer::class);
    }

    // /**
    //  * @return FreeFieldsUserAnswer[] Returns an array of FreeFieldsUserAnswer objects
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
    public function findOneBySomeField($value): ?FreeFieldsUserAnswer
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
