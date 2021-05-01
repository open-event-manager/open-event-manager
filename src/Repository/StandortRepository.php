<?php

namespace App\Repository;

use App\Entity\Standort;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Standort|null find($id, $lockMode = null, $lockVersion = null)
 * @method Standort|null findOneBy(array $criteria, array $orderBy = null)
 * @method Standort[]    findAll()
 * @method Standort[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StandortRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Standort::class);
    }

    // /**
    //  * @return Server[] Returns an array of Server objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Server
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findServerWithEmailandUrl($serverUrl, $email,$apiKey): ?Standort
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user','user')
            ->andWhere('user.email = :email')
            ->setParameter('email', $email)
            ->andWhere('s.url = :url')
            ->andWhere('s.apiKey = :apiKey')
            ->setParameter('url', $serverUrl)
            ->setParameter('apiKey', $apiKey)

            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
