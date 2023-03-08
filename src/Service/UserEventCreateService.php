<?php

namespace App\Service;

use App\Entity\Rooms;
use App\Entity\User;
use App\Entity\UserEventCreated;
use Doctrine\ORM\EntityManagerInterface;

class UserEventCreateService
{
    private $em;
     public function __construct(EntityManagerInterface $entityManager)
     {$this->em = $entityManager;

     }
     public function createEvent(User $user,Rooms $event){
         $createdAt = $this->em->getRepository(UserEventCreated::class)->findOneBy(array('user'=>$user,'event'=>$event));
         if (!$createdAt){
             $createdAt = new UserEventCreated();
         }
            $createdAt->setCreatedAt(new \DateTime())
                ->setUser($user)
                ->setEvent($event);
            $this->em->persist($createdAt);
            $this->em->flush();
     }
     public function generateCreatedString(User $user, Rooms  $rooms):string{
         $createdAt = $this->em->getRepository(UserEventCreated::class)->findOneBy(array('user'=>$user,'event'=>$rooms));
         if (!$createdAt){
             return '';
         }
         return $createdAt->getCreatedAt()->format('d.m.Y H:i');
     }

}