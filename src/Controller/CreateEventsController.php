<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Form\Type\CreaterType;
use App\Service\CloneService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateEventsController extends AbstractController
{
    /**
     * @Route("/room/create/events", name="create_events")
     */
    public function index(Request $request, TranslatorInterface $translator, CloneService $cloneService): Response
    {
        $roomOld = $this->getDoctrine()->getRepository(Rooms::class)->find($request->get('roomId'));
        $creator = array('amount'=>0,'distance'=>0,'unit'=>'min','addUsers'=>false);
        $form = $this->createForm(CreaterType::class, $creator, ['action' => $this->generateUrl('create_events', ['roomId' => $roomOld->getId()])]);

        try {
            $form->handleRequest($request);
        } catch (\Exception $e) {
            $snack = $translator->trans('Fehler, Bitte kontrollieren Sie ihre Daten.');
            return $this->redirectToRoute('dashboard', array('snack' => $snack, 'color' => 'danger'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $creator = $form->getData();
            // here we dublicate the events
            $em = $this->getDoctrine()->getManager();
            for ($i = 0; $i < $creator['amount'];$i++) {
                    $roomOld=$cloneService->cloneEvent($roomOld,$creator['distance'],$creator['unit'],$creator['addUsers']);
            }
            return $this->redirectToRoute('dashboard');

        }
        return $this->render('create_events/index.html.twig', [
            'title'=>$translator->trans('Diesen Termin N mal wiederholen'),
            'form'=>$form->createView()
        ]);
    }
}
