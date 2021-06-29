<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Rooms;
use App\Entity\User;
use App\Service\NotificationService;
use App\Service\ParticipantsStornoService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class StornoController extends AbstractController
{
    /**
     * @Route("/public/storno/start/{uidRoom}/{uidUser}", name="storno_index")
     */
    public function index($uidRoom, $uidUser): Response
    {
        $rooms = $this->getDoctrine()->getRepository(Rooms::class)->findOneBy(array('uid'=>$uidRoom));
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('uid'=>$uidUser));
        if(!$user || !$rooms){
            throw new NotFoundHttpException('Not found');
        }
        if(!in_array($user,$rooms->getUser()->toArray())){
            throw new NotFoundHttpException('User not registered in Event');
        }


        return $this->render('storno/index.html.twig', [
            'user'=>$user,
            'room'=>$rooms,
        ]);
    }
    /**
     * @Route("/public/storno/accept/{uidRoom}/{uidUser}", name="storno_accept")
     */
    public function accept(ParticipantsStornoService $participantsStornoService,$uidRoom, $uidUser, UserService $userService,TranslatorInterface $translator, NotificationService $notificationService): Response
    {
        $rooms = $this->getDoctrine()->getRepository(Rooms::class)->findOneBy(array('uid'=>$uidRoom));
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('uid'=>$uidUser));
        $group = $this->getDoctrine()->getRepository(Group::class)->findOneBy(array('rooms'=>$rooms,'leader'=>$user));
        if(!$user || !$rooms){
            throw new NotFoundHttpException('Not found');
        }
        if(!in_array($user,$rooms->getUser()->toArray())){
            throw new NotFoundHttpException('User not registered in Event');
        }
        $participantsStornoService->removeParticipants($rooms,$user,$group);
        if(sizeof($rooms->getWaitinglists())>0){
            $contentModerator = $this->renderView('email/userCancelSubscription.html.twig',array('room'=>$rooms));
            $subjectModerator= $translator->trans('Stornierung einer Teilnahme in dem Event {name}',array('{name}'=>$rooms->getName()));
            $notificationService->sendNotification($contentModerator,$subjectModerator,$rooms->getModerator(),$rooms->getStandort());
        }

        return $this->render('storno/accept.html.twig', [
            'user'=>$user,
            'room'=>$rooms,
        ]);
    }
}
