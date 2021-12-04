<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Checklist;
use App\Entity\MyUser;
use App\Entity\Rooms;
use App\Entity\Standort;
use App\Service\LicenseService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function GuzzleHttp\Psr7\str;

class RoomsInFuture extends AbstractExtension
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
            new TwigFilter('roomsinFuture', [$this, 'roomsinFuture']),
        ];
    }

    public function roomsinFuture(Standort $standort)
    {
        $now = new \DateTime();
        $qb = $this->em->getRepository(Rooms::class)->createQueryBuilder('rooms');
        $qb->andWhere('rooms.standort = :standort')
            ->andWhere('rooms.showRoomOnJoinpage = true')
            ->andWhere('rooms.start > :now')
            ->andWhere($qb->expr()->isNotNull('rooms.moderator'))
            ->setParameter('standort', $standort)
            ->setParameter('now', $now)
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('rooms.showAfterDate'),
                $qb->expr()->lte('rooms.showAfterDate', ':now')
            ))
            ->setParameter('now', $now)
            ->orderBy('rooms.start', 'ASC')
            ->setParameter('now', new \DateTime());
        $rooms = $qb->getQuery()->getResult();

        return $rooms;

    }

}