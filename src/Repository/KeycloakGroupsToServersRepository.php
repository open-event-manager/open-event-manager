<?php

namespace App\Repository;

use App\Entity\KeycloakGroupsToStandorts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KeycloakGroupsToStandorts|null find($id, $lockMode = null, $lockVersion = null)
 * @method KeycloakGroupsToStandorts|null findOneBy(array $criteria, array $orderBy = null)
 * @method KeycloakGroupsToStandorts[]    findAll()
 * @method KeycloakGroupsToStandorts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeycloakGroupsToServersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KeycloakGroupsToStandorts::class);
    }

    // /**
    //  * @return KeycloakGroupsToServers[] Returns an array of KeycloakGroupsToServers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KeycloakGroupsToServers
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
