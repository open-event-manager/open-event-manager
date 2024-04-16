<?php

namespace App\Service;

use App\Entity\Rooms;
use App\Entity\User;
use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class TransferOwnerShipService
{
    private $entityManager;
    public function __construct(
         EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function transferOwnerShip(Rooms $room, User $newOwner)
    {
        if (!$newOwner->getKeycloakId()) {
            return false;
        }
        $room->setModerator($newOwner);
        $this->entityManager->persist($room);
        $this->entityManager->flush();
        return true;
    }
}