<?php
/**
 * Created by PhpStorm.
 * User: andreas.holzmann
 * Date: 15.05.2020
 * Time: 09:15
 */

namespace App\Controller;

use App\Entity\Rooms;
use App\Entity\Standort;
use App\Entity\User;
use App\Form\Type\JoinViewType;
use App\Service\RoomSpaceService;
use App\Service\ServerUserManagment;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * Class DashboardController
 * @package App\Controller
 */
class DashboardController extends AbstractController
{

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, RoomSpaceService $roomSpaceService)
    {
        $qb = $this->getDoctrine()->getRepository(Rooms::class)->createQueryBuilder('rooms');
        $now = new \DateTime();
        $qb->andWhere('rooms.showRoomOnCalendar = true')
            ->andWhere($qb->expr()->isNotNull('rooms.moderator'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('rooms.showAfterDate'),
                $qb->expr()->lte('rooms.showAfterDate',':now')
            ))
        ->setParameter('now',$now);
        $tmp = $qb->getQuery()->getResult();
        $events = array();
        foreach ($tmp as $data) {
            if ($roomSpaceService->isRoomSpace($data) || $data->getShowInCalendarWhenNoSpace() == null) {
                $events[] = array('data' => $data, 'space' => $roomSpaceService->isRoomSpace($data));
            }
        }
        return $this->render('dashboard/start.html.twig', ['events' => $events]);
    }


    /**
     * @Route("/room/dashboard", name="dashboard")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function dashboard(Request $request, ServerUserManagment $serverUserManagment)
    {
        if ($request->get('join_room') && $request->get('type')) {
            return $this->redirectToRoute('room_join', ['room' => $request->get('join_room'), 't' => $request->get('type')]);
        }

        $roomsFuture = $this->getDoctrine()->getRepository(Rooms::class)->findRoomsInFuture($this->getUser());
        $r = array();
        $future = array();
        foreach ($roomsFuture as $data) {
            $future[$data->getStart()->format('Ymd')][] = $data;
        }
        $roomsPast = $this->getDoctrine()->getRepository(Rooms::class)->findRoomsInPast($this->getUser());
        $roomsNow = $this->getDoctrine()->getRepository(Rooms::class)->findRuningRooms($this->getUser());
        $roomsToday = $this->getDoctrine()->getRepository(Rooms::class)->findTodayRooms($this->getUser());

        $standort = $serverUserManagment->getStandortsFromUser($this->getUser());
        if (!$this->getUser()->getUid()) {
            $user = $this->getUser();
            $user->setUid(md5(uniqid()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return $this->render('dashboard/index.html.twig', [
            'roomsFuture' => $future,
            'roomsPast' => $roomsPast,
            'runningRooms' => $roomsNow,
            'todayRooms' => $roomsToday,
            'snack' => $request->get('snack'),
            'standort' => $standort,
        ]);
    }

}
