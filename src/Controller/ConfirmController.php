<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Service\LoggerService;
use App\Service\SubcriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmController extends AbstractController
{
    private $logger;

    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/room/confirm/manuell", name="confirm_manuell")
     */
    public function index(Request $request, SubcriptionService $subcriptionService): Response
    {
        $subscriber = $this->getDoctrine()->getRepository(Subscriber::class)->find($request->get('id'));
        if ($subscriber->getRoom()->getModerator() !== $this->getUser()) {
            throw new NotFoundHttpException('Event nicht gefunden');
        }
        $this->logger->log('Manual Double-Opt-In Participant',
            array(
                'participantId' => $subscriber->getUser()->getId(),
                'participantEmail' => $subscriber->getUser()->getEmail(),
                'eventId'=>$subscriber->getRoom()->getId(),
                'event'=>$subscriber->getRoom()->getName(),
                'user'=>$this->getUser()->getEmail()
            ));
        $subcriptionService->acceptSub($subscriber, true);
        return $this->redirectToRoute('dashboard');

    }
}
