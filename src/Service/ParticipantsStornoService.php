<?php


namespace App\Service;


use App\Entity\Group;
use App\Entity\Rooms;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ParticipantsStornoService
{
    private $em;
    private $userService;
    public function __construct(EntityManagerInterface $entityManager, UserService $userService)
    {
        $this->em = $entityManager;
        $this->userService = $userService;
    }
    public function removeParticipants(Rooms $rooms,User $user,Group $group){
        $rooms->removeUser($user);
        $rooms->addStorno($user);
        $this->em->persist($rooms);
        $this->em->flush();
        $this->userService->removeRoom($user,$rooms);
        if($group){
            foreach ($group->getMembers() as $data){
                $rooms->removeUser($data);
                $rooms->addStorno($data);
                $group->removeMember($data);
                $this->userService->removeRoom($data,$rooms);
                $this->em->persist($rooms);
                $this->em->persist($group);

            }
            $this->em->flush();
            $this->em->remove($group);
            $this->em->flush();
        }
        $this->em->flush();
    }
}