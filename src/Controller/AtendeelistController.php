<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Service\TeilnehmerExcelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AtendeelistController extends AbstractController
{
    /**
     * @Route("/room/atendeelist/download/excel/{uid}", name="atendeelist_download_excel")
     */
    public function index($uid, TeilnehmerExcelService $teilnehmerExcelService): Response
    {
        $rooms = $this->getDoctrine()->getRepository(Rooms::class)->findOneBy(array('uid'=>$uid));
        if(!$rooms){
            throw new NotFoundHttpException('Not found');
        }
        $teilnehmerExcelService->generateTeilnehmerliste($rooms);
        return $this->file($teilnehmerExcelService->generateTeilnehmerliste($rooms), $rooms->getName() . '.xlsx', ResponseHeaderBag::DISPOSITION_INLINE);


    }
}
