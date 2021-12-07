<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Service\TeilnehmerExcelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Doctrine\ORM\QueryBuilder;

class DayListController extends AbstractController
{
    /**
     * @Route("/room/day/list", name="day_list")
     */
    public function index(Request $request, TeilnehmerExcelService $teilnehmerExcelService): Response
    {
        $from = $request->get('from') ? new \DateTime($request->get('from')) : new \DateTime();
        $from->setTime(0, 0);
        $to = $request->get('to') ? new \DateTime($request->get('to')) : new \DateTime();
        $to->setTime(23, 59);
        $qb = $this->getDoctrine()->getRepository(Rooms::class)->createQueryBuilder('rooms');
        $rooms = $qb->andWhere($qb->expr()->gte('rooms.start', ':from'))
            ->andWhere($qb->expr()->lte('rooms.start', ':to'))
            ->andWhere('rooms.moderator = :moderator')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('moderator', $this->getUser())
            ->getQuery()
            ->getResult();
        return $this->file($teilnehmerExcelService->generateTeilnehmerDayList($rooms,md5(uniqid()) ), $from->format('d.m.Y').' - '.$to->format('d.m.Y') . '.xlsx', ResponseHeaderBag::DISPOSITION_INLINE);
    }
    /**
     * @Route("/room/day/list/modal", name="day_list_modal")
     */
    public function modal(Request $request, TeilnehmerExcelService $teilnehmerExcelService,TranslatorInterface $translator): Response
    {

        return $this->render('day_list/index.html.twig',array('title'=>$translator->trans('Export eines Zeitraums')));
    }
}
