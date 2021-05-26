<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Checklist;
use App\Entity\Group;
use App\Entity\MyUser;
use App\Entity\Rooms;
use App\Entity\Standort;
use App\Entity\User;
use App\Entity\Waitinglist;
use App\Service\LicenseService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function GuzzleHttp\Psr7\str;

class GroupUtils extends AbstractExtension
{


    private $licenseService;
    private $em;

    public function __construct(EntityManagerInterface $entityManager, LicenseService $licenseService, TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->licenseService = $licenseService;
        $this->em = $entityManager;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('groupFromWaitinglist', [$this, 'groupFromWaitinglist']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('atendeeIsInGroup', [$this, 'atendeeIsInGroup']),
            new TwigFunction('getUserWhichAreNotMemeber', [$this, 'getUserWhichAreNotMemeber']),
            new TwigFunction('userFromUsergroup', [$this, 'userFromUsergroup']),
        ];
    }

    public function groupFromWaitinglist(Waitinglist $waitinglist)
    {
        return $this->em->getRepository(Group::class)->findOneBy(array('leader' => $waitinglist->getUser(), 'rooms' => $waitinglist->getRoom()));

    }

    public function userFromUsergroup(User $user, Rooms $rooms)
    {
        return $this->em->getRepository(User::class)->userFromLeaderAndRoom($user, $rooms);
    }

    public function atendeeIsInGroup(User $user, Rooms $rooms)
    {
      return $this->em->getRepository(Group::class)->atendeeIsInGroup($user, $rooms);
    }
    public function getUserWhichAreNotMemeber(User $user, Rooms $rooms)
    {
        return $this->em->getRepository(Group::class)->atendeeIsInGroup($user, $rooms);
    }
}