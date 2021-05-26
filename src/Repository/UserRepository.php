<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Rooms;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
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
    public function findOneByEmail($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findMyUserByEmail($value, User $user)
    {
        $qb = $this->createQueryBuilder('u')
            ->innerJoin(' u.addressbookInverse', 'user')
            ->andWhere('user = :user')
            ->setParameter('user', $user);

        return $qb->andWhere($qb->expr()->like('u.email', ':search'))
            ->setParameter('search', '%' . $value . '%')

            ->getQuery()
            ->getResult();

    }
    public function userFromLeaderAndRoom(User $user, Rooms $rooms)
    {
        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.rooms', 'rooms')
            ->andWhere('rooms = :room')
            ->innerJoin('u.eventGroupsMemebers', 'g')
            ->innerJoin('g.leader','leader')
            ->andWhere('leader = :leader')
            ->setParameter('leader',$user)
            ->setParameter('room',$rooms)
            ->getQuery();
        return $qb->getResult();

    }
    public function findSubsriberLeaders(Rooms $rooms)
    {
        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.subscribers', 'subscribers')
            ->innerJoin('subscribers.room', 'room')
            ->andWhere('room = :room')
            ->innerJoin('u.eventGroups','g')
            ->innerJoin('g.rooms','r')
            ->andWhere('r = :room')
            ->setParameter('room',$rooms)
            ->getQuery();
        return $qb->getResult();
    }
    public function findWaitingListLeaders(Rooms $rooms)
    {
        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.waitinglists', 'waitinglists')
            ->innerJoin('waitinglists.room', 'room')
            ->andWhere('room = :room')
            ->innerJoin('u.eventGroups','g')
            ->innerJoin('g.rooms','r')
            ->andWhere('r = :room')
            ->setParameter('room',$rooms)
            ->getQuery();
        return $qb->getResult();
    }
    public function findActiveGroupLeaders(Rooms $rooms)
    {
        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.rooms', 'r')
            ->andWhere('r = :room')
            ->innerJoin('u.eventGroups','g')
            ->innerJoin('g.rooms','room')
            ->andWhere('room = :room')
            ->setParameter('room',$rooms)
            ->getQuery();
        return $qb->getResult();
    }
}
