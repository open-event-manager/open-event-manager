<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Checklist;
use App\Entity\MyUser;
use App\Entity\Rooms;
use App\Entity\Standort;
use App\Service\LicenseService;
use App\Service\MessageService;
use App\Service\RoomSpaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function GuzzleHttp\Psr7\str;

class RoomhasSPace extends AbstractExtension
{


   private $roomSpaceService;
    private $em;

    public function __construct(RoomSpaceService $roomSpaceService)
    {
        $this->roomSpaceService = $roomSpaceService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('roomHasSpace', [$this, 'roomHasSpace']),
        ];
    }

    public function roomHasSpace(Rooms $rooms)
    {
        return $this->roomSpaceService->isRoomSpace($rooms);

    }

}