<?php


namespace App\Service;


use App\Entity\Group;
use App\Entity\Rooms;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RoomSpaceService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function isRoomSpace(Rooms $rooms)
    {

        if($rooms->getMaxParticipants() == null){
            return true;
        }
        if(sizeof($rooms->getUser()->toArray()) < $rooms->getMaxParticipants()){
            return true;
        }
        if($rooms->getWaitinglist()){
            if(!$rooms->getMaxWaitingList()){
                return  true;
            }
            $userinGroups = sizeof($this->em->getRepository(User::class)->findUserInWaitinglists($rooms));
            $waitinglists = sizeof($rooms->getWaitinglists()->toArray());
            if(($userinGroups + $waitinglists) < $rooms->getMaxWaitingList()){
                return true;
            }
        }
        return false;
    }

    public function leftRoomSpace(Rooms $rooms)
    {

        if($rooms->getMaxParticipants() == null){
            return true;
        }
        if(sizeof($rooms->getUser()->toArray()) < $rooms->getMaxParticipants()){
            return $rooms->getMaxParticipants() - sizeof($rooms->getUser()->toArray());
        }
        if($rooms->getWaitinglist()){
            if(!$rooms->getMaxWaitingList()){
                return  true;
            }
            $userinGroups = sizeof($this->em->getRepository(User::class)->findUserInWaitinglists($rooms));
            $waitinglists = sizeof($rooms->getWaitinglists()->toArray());
            if(($userinGroups + $waitinglists) < $rooms->getMaxWaitingList()){
                return $rooms->getMaxWaitingList() - ($userinGroups + $waitinglists);
            }
        }
        return 0;
    }

}