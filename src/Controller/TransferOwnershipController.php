<?php

namespace App\Controller;

use App\Repository\RoomsRepository;
use App\Repository\RoomsUserRepository;
use App\Repository\UserRepository;
use App\Service\TransferOwnerShipService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferOwnershipController extends AbstractController
{
    private $userRepository;
    private $roomsRepository;
    private $transferOwnerShipService;

    public function __construct(
        UserRepository           $userRepo,
        RoomsRepository          $roomsRepository,
        TransferOwnerShipService $transferOwnerShipService
    )
    {
        $this->userRepository = $userRepo;
        $this->roomsRepository = $roomsRepository;
        $this->transferOwnerShipService = $transferOwnerShipService;
    }

    /**
     * @Route("/transfer/ownership", name="transfer_ownership")
     */
    public function index(Request $request): Response
    {
        $newOwner = $this->userRepository->find($request->get('new_user'));
        $room = $this->roomsRepository->find($request->get('room_id'));
        if ($room->getModerator() === $this->getUser()) {
            $this->transferOwnerShipService->transferOwnerShip( $room, $newOwner);
        }

        return $this->redirectToRoute('dashboard');


    }
}
