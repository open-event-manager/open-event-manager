<?php

namespace App\Service;

use App\Entity\Rooms;
use Doctrine\ORM\EntityManagerInterface;

class CloneService
{
    private $userService;
    private $em;

    public function __construct(UserService $userService, EntityManagerInterface $entityManager)
    {
        $this->userService = $userService;
        $this->em = $entityManager;
    }

    public function cloneEvent(Rooms $rooms, $distance, $unit, $addUser)
    {
        $room = clone $rooms;
        $freeFieldsOld = $rooms->getFreeFields()->toArray();
        foreach ($rooms->getFreeFields() as $data) {
            $freeTmp = clone $data;
            $room->addFreeField($freeTmp);
        }
        foreach ($freeFieldsOld as $data) {
            $room->removeFreeField($data);
        }
        $room->setStart($room->getStart()->modify('+' . $distance . $unit));
        if ($room->getEntryDateTime()) {
            $room->setEntryDateTime($room->getEntryDateTime()->modify('+' . $distance . $unit));
        }

        $room->setEnddate($room->getEnddate()->modify('+' . $distance . $unit));
        $room->setUid(rand(01, 99) . md5(uniqid()));
        $room->setSequence(0);
        $room->setUidReal(md5(uniqid('h2-invent', true)));
        $room->setUidModerator(md5(uniqid()));
        $room->setUidParticipant(md5(uniqid()));
        $this->userService->addUser($room->getModerator(), $room);
        if ($addUser) {
            foreach ($rooms->getUser() as $user) {
                $this->userService->addUser($user, $room);
            }
        }

        $this->em->persist($room);
        $this->em->flush();
        return $room;
    }
}