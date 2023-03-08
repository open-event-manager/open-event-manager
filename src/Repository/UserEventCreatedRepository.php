<?php

namespace App\Repository;

use App\Entity\UserEventCreated;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserEventCreated|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEventCreated|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEventCreated[]    findAll()
 * @method UserEventCreated[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserEventCreatedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEventCreated::class);
    }

    // /**
    //  * @return UserEventCreated[] Returns an array of UserEventCreated objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserEventCreated
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
