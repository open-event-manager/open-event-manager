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
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SubcriptionService
{
    private $em;
    private $twig;
    private $translator;
    private $notifier;
    private $userService;
    private $logger;
    public function __construct(LoggerInterface $logger, UserService $userService, NotificationService $notificationService, EntityManagerInterface $entityManager, Environment $environment, TranslatorInterface $translator)
    {
        $this->em = $entityManager;
        $this->twig = $environment;
        $this->translator = $translator;
        $this->notifier = $notificationService;
        $this->userService = $userService;
        $this->logger = $logger;
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
        $groupEmail = array(strtolower($userData['email']));
        foreach ($group as $data){
            if ($data['email'] !== ''){
                $groupEmail[] = strtolower($data['email']);
            }

        }
        if(sizeof($groupEmail) !== sizeof(array_unique($groupEmail))){
            $res['text'] = $this->translator->trans('Sie haben doppelte E-Mail Adressen eingetragen');
            $res['color'] = 'danger';
            return $res;
        }
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => strtolower($userData['email'])));
        //create a new User from the email entered
        if (!$user) {
            $user = new User();
            $user->setEmail(strtolower($userData['email']));
        }

        $user->setFirstName($userData['firstName']);
        $user->setLastName($userData['lastName']);
        $user->setPhone($userData['phone']);
        $user->setAddress($userData['address']);
        $this->em->persist($user);
        $this->em->flush();
        $this->logger->info('Added a new User to the database',array('email'=>$user->getEmail(),'id'=>$user->getId()));
        $subscriber = $this->em->getRepository(Subscriber::class)->findOneBy(array('room' => $rooms, 'user' => $user));

        if ($subscriber) {
            if (!$isOrganizer) {
                $this->notifier->sendNotification(
                    $this->twig->render('email/subscriptionToRoom.html.twig', array('room' => $rooms, 'subsription' => $subscriber)),
                    $this->translator->trans('Bestätigung ihrer Anmeldung zur Veranstaltung: {name}', array('{name}' => $rooms->getName())),
                    $user,
                    $rooms->getStandort()
                );
                $this->logger->info('User tries to readd himself',array('email'=>$user->getEmail(),'id'=>$user->getId()));
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
            $this->logger->info('New Subscriber created',array('subscriberId'=>$subscriber->getUid(),'email'=>$user->getEmail(),'id'=>$user->getId()));
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
                $this->logger->info('Send Email with Double opt in',array('email'=>$user->getEmail(),'id'=>$user->getId()));
                $this->notifier->sendNotification(
                    $this->twig->render('email/subscriptionToRoom.html.twig', array('room' => $rooms, 'subsription' => $subscriber)),
                    $this->translator->trans('Bestätigung ihrer Anmeldung zur Veranstaltung: {name}', array('{name}' => $rooms->getName())),
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
        $this->logger->info('The Subsrciber tries to add himself as a event Particpant',array('subscriberId'=>$subscriber->getUid(),'email'=>$subscriber->getUser()->getEmail(),'id'=>$subscriber->getUser()->getId(),'event'=>$subscriber->getRoom()->getId()));

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
                $this->logger->info('The User could not be added. the Event is full',array('email'=>$subscriber->getUser()->getEmail(),'id'=>$subscriber->getUser()->getId(),'event'=>$room->getId()));

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
                $this->logger->info('The User could not be added. the Event is full',array('email'=>$subscriber->getUser()->getEmail(),'id'=>$subscriber->getUser()->getId(),'event'=>$room->getId()));

                return $res;
            }
        }

        // add a new waiting list or a new participant
        try {
            $waitinglist = false;
            if ($subscriber->getRoom()->getMaxParticipants() != null && $newParticipants > $subscriber->getRoom()->getMaxParticipants()) {
                $waitinglist = true;
            }
            if ($waitinglist === true && !$isOrganizer) {
                $res = array_merge($res, $this->createNewWaitinglist($subscriber->getUser(), $subscriber->getRoom()));
                $this->logger->info('The User is added to the waitinglist',array('email'=>$subscriber->getUser()->getEmail(),'id'=>$subscriber->getUser()->getId(),'event'=>$room->getId()));

                $this->em->remove($subscriber);
                $this->em->flush();
            } else {
                $this->createUserRoom($subscriber->getUser(), $subscriber->getRoom());
                $this->logger->info('The User is added to the event',array('email'=>$subscriber->getUser()->getEmail(),'id'=>$subscriber->getUser()->getId(),'event'=>$room->getId()));

                $this->em->remove($subscriber);
                $this->em->flush();
            }


        } catch (\Exception $exception) {
            $this->logger->info('An Error ocured ans the USer is asked to click the link again',array('email'=>$subscriber->getUser()->getEmail(),'id'=>$subscriber->getUser()->getId(),'event'=>$room->getId()));

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
        $this->logger->info('User is added to the Event',array('email'=>$user->getEmail(),'id'=>$user->getId(),'event'=>$rooms->getId()));

        if ($group) {
            foreach ($group->getMembers() as $data) {
                if (!in_array($data, $rooms->getUser()->toArray())) {
                    $rooms->addUser($data);
                    $this->logger->info('Groupmember is added to the Event',array('email'=>$data->getEmail(),'id'=>$data->getId(),'event'=>$rooms->getId(),'leader'=>$user->getId()));
                    $rooms->removeStorno($data);
                    $this->em->persist($data);
                    $this->userService->addUser($data, $rooms);
                } else {
                    $group->removeMember($data);

                }
            }
            $this->em->persist($group);
        }
        $this->userService->addUser($user, $rooms);
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
            $this->logger->info('Group is created with leader',array('email'=>$user->getEmail(),'id'=>$user->getId()));

            foreach ($groups as $data) {
                $email = strtolower($data['email']);
                $firstName = $data['firstName'];
                $lastName = $data['lastName'];
                $address = $data['address'];
                if ($firstName !== '' && $lastName !== '' && $address != '') {

                    // this entry is correct
                    $member = null;
                    if ($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $member = $this->em->getRepository(User::class)->findOneBy(array('email' => $email));
                        if (!$member) {
                            $member = new User();
                            $member->setEmail($email);

                        }
                    }else{
                        $member = new User();
                    }

                    $member->setFirstName($firstName);
                    $member->setLastName($lastName);
                    $member->setAddress($address);
                    $this->em->persist($member);
                    if (!in_array($member, $rooms->getUser()->toArray())
                        && !in_array($user, $this->em->getRepository(User::class)->findSubsriberLeaders($rooms))
                        && !in_array($user, $this->em->getRepository(User::class)->findWaitingListLeaders($rooms))
                    ) {
                        $this->logger->info('Member is added to gorup',array('group'=>$group->getId(),'email'=>$member->getEmail(),'id'=>$user->getId()));
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