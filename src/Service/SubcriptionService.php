<?php


namespace App\Service;


use App\Entity\Group;
use App\Entity\Rooms;
use App\Entity\RoomsUser;
use App\Entity\Subscriber;
use App\Entity\User;
use App\Entity\Waitinglist;
use App\Repository\RoomsUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SubcriptionService
{
    private $em;
    private $twig;
    private $translator;
    private $notifier;
    private $userService;

    public function __construct(UserService $userService, NotificationService $notificationService, EntityManagerInterface $entityManager, Environment $environment, TranslatorInterface $translator)
    {
        $this->em = $entityManager;
        $this->twig = $environment;
        $this->translator = $translator;
        $this->notifier = $notificationService;
        $this->userService = $userService;
    }

    /**
     * @param $userData
     * @param Rooms $rooms
     * @param false $moderator
     * @return array|bool[]
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * Creates a subscriber. A subscriber is not a participent. a subscriber need to double opt in
     * This functions sends a mail to the subscriper with the double opt in link.
     * This function checks if the room is full and if so then it will reject or if the waiting list is active then the user can register
     */
    public function subscripe($userData, Rooms $rooms, $isOrganizer, $group = array(), $moderator = false)
    {
        $res = array('error' => true);
        if ($isOrganizer) {
            if ($rooms->getMaxParticipants() && (sizeof($rooms->getUser()->toArray()) >= $rooms->getMaxParticipants()) && $rooms->getWaitinglist() != true) {
                $res['text'] = $this->translator->trans('Die maximale Teilnehmeranzahl ist bereits erreicht.');
                $res['color'] = 'danger';
                return $res;
            }
        }
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $res['text'] = $this->translator->trans('Ungültige Email. Bitte überprüfen Sie ihre Emailadresse.');
            $res['color'] = 'danger';
            return $res;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => $userData['email']));
        //create a new User from the email entered
        if (!$user) {
            $user = new User();
            $user->setEmail($userData['email']);
        }

        $user->setFirstName($userData['firstName']);
        $user->setLastName($userData['lastName']);
        $user->setPhone($userData['phone']);
        $this->em->persist($user);
        $this->em->flush();

        $subscriber = $this->em->getRepository(Subscriber::class)->findOneBy(array('room' => $rooms, 'user' => $user));

        if ($subscriber) {
            if (!$isOrganizer) {
                $this->notifier->sendNotification(
                    $this->twig->render('email/subscriptionToRoom.html.twig', array('room' => $rooms, 'subsription' => $subscriber)),
                    $this->translator->trans('Bestätigung ihrer Anmeldung zur Konferenz: {name}', array('{name}' => $rooms->getName())),
                    $user,
                    $rooms->getStandort()
                );

                $res['text'] = $this->translator->trans('Sie haben sich bereits angemeldet. Wir haben Ihnen den Link erneut zugesandt. Bite bestätigen sie noch ihre Anmeldung durch klick auf den Link in der Email.');
                $res['color'] = 'danger';
                $res['error'] = false;
            }

        } elseif (in_array($rooms, $user->getRooms()->toArray())) {
            $res['text'] = $this->translator->trans('Sie haben sich bereits angemeldet.');
            $res['color'] = 'danger';
            $res['error'] = false;
        } else {
            $res = $this->createNewSubscriber($user, $rooms);
            $subscriber = $res['sub'];
            if ($moderator == true) {
                $usersRoom = new RoomsUser();
                $usersRoom->setRoom($rooms);
                $usersRoom->setUser($user);
                $usersRoom->setModerator(true);
                $usersRoom->setPrivateMessage(true);
                $usersRoom->setShareDisplay(true);
                $this->em->persist($usersRoom);
                $this->em->flush();
            }
            if (!$isOrganizer) {
                $this->notifier->sendNotification(
                    $this->twig->render('email/subscriptionToRoom.html.twig', array('room' => $rooms, 'subsription' => $subscriber)),
                    $this->translator->trans('Bestätigung ihrer Anmeldung zur Konferenz: {name}', array('{name}' => $rooms->getName())),
                    $user,
                    $rooms->getStandort()
                );
            }
            $this->createGroup($user, $group, $rooms);
        }


        return $res;
    }


    /**
     * @param Subscriber|null $subscriber
     * @return array
     * checks the subsriber an creates a roomUser connection or a waitinglist Element
     */
    public function acceptSub(?Subscriber $subscriber, $isOrganizer)
    {
        $res['message'] = $this->translator->trans('Danke für die Anmeldung. ');
        $res['title'] = $this->translator->trans('Erfolgreich bestätigt');
        if (!$subscriber) {
            $res['message'] = $this->translator->trans('Dieser Link ist ungültig. Wahrscheinlich wurde er bereits bestätigt.');
            $res['title'] = $this->translator->trans('Fehler');
            return $res;
        }

        $group = $this->em->getRepository(Group::class)->findOneBy(array('leader' => $subscriber->getUser(), 'rooms' => $subscriber->getRoom()));
        $room = $subscriber->getRoom();
        $oldParticipants = sizeof($room->getUser());
        $newParticipants = $oldParticipants + 1;
        if ($group) {
            $newParticipants = $newParticipants + sizeof($group->getMembers());
        }
        $oldWaitingLisParticipants = $this->returnWaitinglistmemeber($room);
        $newWaitingLisParticipants = $oldWaitingLisParticipants;
        $newWaitingLisParticipants++;
        if ($group) {
            $newWaitingLisParticipants = $newWaitingLisParticipants + sizeof($group->getMembers());
        }
        if (!$isOrganizer) {
            if (
                $room->getMaxParticipants() != null
                && $newParticipants > $room->getMaxParticipants()
                && $room->getWaitinglist() != true // the Waiting list is not set, and the participants is full
            ) {
                $res['message'] = $this->translator->trans('Die maximale Teilnehmeranzahl ist bereits erreicht.');
                $res['title'] = $this->translator->trans('Fehler');
                return $res;
            }

            if (
                $room->getMaxWaitingList() != null &&
                $newWaitingLisParticipants > $room->getMaxWaitingList()// the waitinglist is enabled and the limit is set, and the maxwaiting list is higher then the allowed
            ) {
                $res['message'] = $this->translator->trans('Die maximale Teilnehmeranzahl ist bereits erreicht.');
                $res['title'] = $this->translator->trans('Fehler');
                return $res;
            }
        }

        // add a new waiting list or a new participant
        try {
            $waitinglist = false;
            if ($subscriber->getRoom()->getMaxParticipants() != null && $newParticipants >= $subscriber->getRoom()->getMaxParticipants()) {
                $waitinglist = true;
            }
            if ($waitinglist === true && !$isOrganizer) {
                $res = array_merge($res, $this->createNewWaitinglist($subscriber->getUser(), $subscriber->getRoom()));
                $this->em->remove($subscriber);
                $this->em->flush();
            } else {
                $this->createUserRoom($subscriber->getUser(), $subscriber->getRoom());
                $this->em->remove($subscriber);
                $this->em->flush();
            }


        } catch (\Exception $exception) {
            $res['message'] = $this->translator->trans('Fehler, Bitte klicken Sie den link erneut an.');
            $res['title'] = $this->translator->trans('Fehler');

        }

        return $res;
    }

    /**
     * @param User $user
     * @param Rooms $rooms
     * @return array
     * creates a new subscriber element
     */
    function createNewSubscriber(User $user, Rooms $rooms)
    {

        $subscriber = new Subscriber();
        $subscriber
            ->setUser($user)
            ->setRoom($rooms)
            ->setUid(md5(uniqid()));
        $this->em->persist($subscriber);
        $this->em->flush();
        $res['text'] = $this->translator->trans('Vielen Dank für die Anmeldung. Bitte bestätigen Sie Ihre Emailadresse in der Email, die wir ihnen zugeschickt haben.');
        $res['color'] = 'success';
        $res['error'] = false;
        $res['sub'] = $subscriber;
        return $res;
    }

    /**
     * @param User $user
     * @param Rooms $rooms
     * @return array
     * creates a new Waiinglist element and sends the email with the waiting list to the subscriber
     */
    function createNewWaitinglist(User $user, Rooms $rooms)
    {

        $waitingList = new Waitinglist();
        $waitingList->setUser($user)->setRoom($rooms)->setCreatedAt(new \DateTime());
        $this->em->persist($waitingList);
        $this->em->flush();
        $res['text'] = $this->translator->trans('Sie sind auf der Warteliste');
        $res['color'] = 'success';
        $res['error'] = false;
        $this->userService->addWaitinglist($user, $rooms);
        return $res;
    }

    /**
     * @param User $user
     * @param Rooms $rooms
     * creates a new roomUser element and sends the email with the room infos  to the new participant
     */
    function createUserRoom(User $user, Rooms $rooms)
    {
        $group = $this->em->getRepository(Group::class)->findOneBy(array('leader' => $user, 'rooms' => $rooms));
        $rooms->addUser($user);
        $rooms->removeStorno($user);

        $this->userService->addUser($user, $rooms);
        if ($group) {
            foreach ($group->getMembers() as $data) {
                if (!in_array($data, $rooms->getUser()->toArray())) {
                    $rooms->addUser($data);
                    $rooms->removeStorno($data);
                    $this->em->persist($data);
                    $this->userService->addUser($data, $rooms);
                } else {
                    $group->removeMember($data);

                }
            }
            $this->em->persist($group);
        }
        $this->em->persist($rooms);
        $this->em->flush();
    }


    /**
     * @param User $user
     * @param $groups
     * @param Rooms $rooms
     * This create the group The group is coupled to the leader.
     * The group memebers a coupled to the group entity
     * the group leader is connected to the supscriper
     */
    function createGroup(User $user, $groups, Rooms $rooms)
    {
        $group = new Group();
        $group->setLeader($user);
        $group->setRooms($rooms);
        $counter = 0;
        if (sizeof($groups) > 0) {
            foreach ($groups as $data) {
                $email = $data['email'];
                $firstName = $data['firstName'];
                $lastName = $data['lastName'];

                if ($firstName !== '' && $lastName !== '' && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    // this entry is correct
                    $member = $this->em->getRepository(User::class)->findOneBy(array('email' => $email));
                    if (!$member) {
                        $member = new User();
                        $member->setEmail($email);
                    }
                    $member->setFirstName($firstName);
                    $member->setLastName($lastName);
                    $this->em->persist($member);
                    if (!in_array($member, $rooms->getUser()->toArray())
                        && !in_array($user, $this->em->getRepository(User::class)->findSubsriberLeaders($rooms))
                        && !in_array($user, $this->em->getRepository(User::class)->findWaitingListLeaders($rooms))
                    ) {
                        $group->addMember($member);
                        $this->em->persist($group);

                    }
                    $counter++;
                }
            }
            if ($counter > 0) {
                $this->em->flush();
            }
        }
    }

    function returnWaitinglistmemeber(Rooms $rooms)
    {
        $newParticipantsWaitinglist = 0;
        foreach ($rooms->getWaitinglists() as $data) {
            $newParticipantsWaitinglist++;
            $group = $this->em->getRepository(Group::class)->findOneBy(array('leader' => $data->getUser(), 'rooms' => $rooms));
            if ($group) {
                $newParticipantsWaitinglist = $newParticipantsWaitinglist + sizeof($group->getMembers());
            }
        }
        return $newParticipantsWaitinglist;
    }
}