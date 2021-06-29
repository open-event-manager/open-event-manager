<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Entity\User;
use App\Service\UserService;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RepeatSendController extends AbstractController
{
    /**
     * @Route("/rooms/repeat/send", name="room_repeat_send")
     */
    public function index(Request $request,UserService $userService,TranslatorInterface $translator): Response
    {
        $room = $this->getDoctrine()->getRepository(Rooms::class)->find($request->get('id'));
        if($room->getModerator() !== $this->getUser()){
            throw new NotFoundHttpException('Event not Found');
        }
        foreach ($room->getUser() as $data){
            $userService->addUser($data,$room);
        }
        return $this->redirectToRoute('dashboard',array('snack'=>$translator->trans('Teilnehmer wurden eingeladen')));

    }
}
