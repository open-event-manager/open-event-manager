<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Rooms;
use App\Entity\Subscriber;
use App\Entity\User;
use App\Entity\Waitinglist;
use App\Form\Type\PublicRegisterType;
use App\Service\LoggerService;
use App\Service\PexelService;
use App\Service\RoomService;
use App\Service\SubcriptionService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\String\s;

class ShareLinkController extends AbstractController
{
    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager,LoggerService $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;

    }

    /**
     * @Route("/room/share/link/{id}", name="share_link")
     * @ParamConverter("rooms")
     */
    public function index(Rooms $rooms): Response
    {
        if (!$rooms || !$rooms->getModerator() == $this->getUser() || $rooms->getPublic() != true) {
            throw new NotFoundHttpException('Not found');
        }
        return $this->render('share_link/__shareLinkModal.html.twig', array('room' => $rooms));

    }

    /**
     * @Route("/room/share/link/accetwaitinglist/{id}", name="accept_waitingList")
     * @ParamConverter("waitinglist")
     */
    public function waitinglistAccept(Waitinglist $waitinglist, SubcriptionService $subcriptionService): Response
    {
        if ($waitinglist->getRoom()->getModerator() == $this->getUser()) {
            $subcriptionService->createUserRoom($waitinglist->getUser(), $waitinglist->getRoom());
            $this->em->remove($waitinglist);
            $this->em->flush();
            return new JsonResponse(array('error' => false));
        }
        return new JsonResponse(array('error' => true));
    }
    /**
     * @Route("/room/share/link/deniewaitinglist/{id}", name="denie_waitingList")
     * @ParamConverter("waitinglist")
     */
    public function waitinglistDenie(Waitinglist $waitinglist, SubcriptionService $subcriptionService,UserService $userService, TranslatorInterface $translator): Response
    {

        if ($waitinglist->getRoom()->getModerator() == $this->getUser()) {

            $room = $waitinglist->getRoom();
            $user = $waitinglist->getUser();
            $group = $this->getDoctrine()->getRepository(Group::class)->findOneBy(array('rooms' => $room, 'leader' => $user));
            $room->removeUser($user);
                $this->logger->log('Remove User from Event', array('email' => $user->getEmail(), 'id' => $user->getId(), 'event' => $room->getId()));

            $room->addStorno($user);
                $em = $this->getDoctrine()->getManager();
                $em->persist($room);
                if ($user->isMemeberInGroup($room)) {
                    $user->removeEventGroupsMemeber($user->isMemeberInGroup($room));
                    $em->persist($user);
                }
                if ($group) {
                    foreach ($group->getMembers() as $data) {
                            $this->logger->log('Remove Groupmemeber from Event', array('email' => $data->getEmail(), 'id' => $data->getId(), 'event' => $room->getId()));

                        $room->removeUser($data);
                        $room->addStorno($data);
                        $group->removeMember($data);
                        $userService->removeRoom($data, $room);
                        $em->persist($room);
                        $em->persist($group);

                    }
                    $em->flush();
                    $em->remove($group);
                    $em->flush();
                }
                $em->flush();
                $em->remove($waitinglist);
                $em->flush();
                $snack = $translator->trans('Teilnehmer gelöscht');
                $userService->removeRoom($user, $room);
            }
            return new JsonResponse(array('error' => true));

    }
    /**
     * @Route("/subscribe/self/{uid}", name="public_subscribe_participant")
     */
    public function participants($uid, Request $request, SubcriptionService $subcriptionService, TranslatorInterface $translator, PexelService $pexelService): Response
    {
        $rooms = new Rooms();
        $moderator = false;
        $rooms = $this->em->getRepository(Rooms::class)->findOneBy(array('uidParticipant' => $uid, 'public' => true));
        if (!$rooms) {
            $rooms = $this->em->getRepository(Rooms::class)->findOneBy(array('uidModerator' => $uid, 'public' => true));
            if ($rooms) {
                $moderator = true;
            }
        }
        if (!$rooms || $rooms->getModerator() === null) {
            return $this->redirectToRoute('join_index_no_slug', ['snack' => $translator->trans('Fehler, Bitte kontrollieren Sie ihre Daten.'), 'color' => 'danger']);
        }

        $data = array('email' => '','group'=>array());
        $form = $this->createForm(PublicRegisterType::class, $data);
        $form->handleRequest($request);
        $errors = array();
        $snack = $translator->trans('Bitte geben Sie ihre Daten ein');
        $color = 'success';
        $standort = null;
      if($this->getUser() != $rooms->getModerator()){
          if ($rooms->getMaxParticipants() && (sizeof($rooms->getUser()->toArray()) >= $rooms->getMaxParticipants())) {
              $snack = $translator->trans('Die maximale Teilnehmeranzahl ist bereits erreicht.');
              $color = 'danger';
          }

          if ($rooms->getMaxParticipants() && (sizeof($rooms->getUser()->toArray()) >= $rooms->getMaxParticipants()) && $rooms->getWaitinglist() == true) {
              $snack = $translator->trans('Die maximale Teilnehmeranzahl ist bereits erreicht. Aber sie können sich auf die Warteliste einschreiben.');
              $color = 'warning';
              if($rooms->getMaxWaitingList() != null && sizeof($rooms->getWaitinglists()) >= $rooms->getMaxWaitingList()){
                  $snack = $translator->trans('Die maximale Teilnehmeranzahl ist bereits erreicht.');
                  $color = 'danger';
              }
          }
      }


        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $group = $request->get('group',array());

            $isOrganizer = false;
            if ($rooms->getModerator() == $this->getUser()){
                $isOrganizer = true;
            }

            $res = $subcriptionService->subscripe($data, $rooms,$isOrganizer, $group, $moderator);
            if ($isOrganizer && isset($res['sub'])){
                $subcriptionService->acceptSub($res['sub'],true);
                $snack= $translator->trans('Sie haben den Teilnehmer erfolgreich angemeldet');
                $color= 'success';
                $modalUrl = base64_encode($this->generateUrl('room_add_user', array('room' => $rooms->getId())));
                    return $this->redirectToRoute('dashboard', array(
                        'modalUrl'=>$modalUrl,
                        'color' => $color,
                        'snack' => $snack,
                        )
                    );
            }
            $snack = $res['text'];
            $color = $res['color'];

            if (!$res['error']) {
                return $this->redirectToRoute('public_subscribe_participant', array('color' => $color, 'snack' => $snack, 'uid' => $uid));
            }

        }
        $standort = $rooms->getStandort();
        return $this->render('share_link/subscribe.html.twig', [
            'form' => $form->createView(),
            'snack' => $snack,
            'standort' => $standort,
            'room' => $rooms,
            'color' => $color,
        ]);
    }


    /**
     * @Route("/subscribe/optIn/{uid}", name="public_subscribe_doupleOptIn")
     */
    public function doupleoptin($uid, SubcriptionService $subcriptionService, TranslatorInterface $translator, UserService $userService, PexelService $pexelService): Response
    {
        $subscriber = $this->em->getRepository(Subscriber::class)->findOneBy(array('uid' => $uid));
        $res = $subcriptionService->acceptSub($subscriber,false);
        $standort = null;
        if ($subscriber) {
            $standort = $subscriber->getRoom()->getStandort();
        }

        $message = $res['message'];
        $title = $res['title'];

        return $this->render('share_link/subscribeSuccess.html.twig', array('standort' => $standort, 'message' => $message, 'title' => $title));
    }
}
