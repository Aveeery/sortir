<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\Status;
use App\Entity\User;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;

class EventController extends AbstractController
{
    /**
     * @Route("/createEvent", name="create_event")
     */
    public function createEvent(Request $request, EntityManagerInterface $em)
    {
        $event = new Event();

        $idUser = $this->getUser()->getId();
        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);
        $eventform = $this->createForm(EventType::class, $event);

        $eventform->handleRequest($request);

        if ($eventform->isSubmitted() && $eventform->isValid()) {

            $this->initialiazeEvent($eventform, $user, $event, $em);
            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/createEvent.html.twig', [
            'controller_name' => 'EventController',
            'eventForm' => $eventform->createView(),
        ]);
    }

    //définit à l'event un organisateur, un campus, et appelle la fonction stashOrPublishStatus qui définit un statut en fonction du choix de l'utilisateur
    public function initialiazeEvent($form, $user, Event $event, $em)
    {
        $event->setOrganizer($user);
        $campus = $user->getCampus();
        $event->setCampus($campus);
        $event->addAttendee($user);

        //setStatus l'event
        $this->stashOrPublishStatus($form, $event);

        $em->persist($event);
        $em->flush();
    }

    //définit un statut en fonction du choix de l'utilisateur
    public function stashOrPublishStatus($form, $event)
    {
        $statusRepo = $this->getDoctrine()->getRepository(Status::class);
        $status = $statusRepo->getAllStatus();

        if ($form->get('stashEvent')->isClicked()) {
            $event->setStatus($status['Creating']);
        }
        if ($form->get('publishEvent')->isClicked()) {
            $event->setStatus($status['Opened']);
        }
    }

    /**
     * @Route("/event/{id}", name="event_detail")
     */
    public function detail($id)
    {
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $event = $eventRepo->find($id);
        $attendees = $eventRepo->findAttendees($id);

        return $this->render('event/detail.html.twig', [
            'event' => $event,
            'attendees' => $attendees
        ]);
    }

    /**
     * @Route("/places", name="show_places")
     */
    public
    function showPlaces(Request $request)
    {
        $idCity = $request->query->get('id');
        $placeRepo = $this->getDoctrine()->getRepository(Place::class);
        $idPlaces = $placeRepo->findAllByCity($idCity);

        return new JsonResponse(json_encode($idPlaces));
    }

    /**
     * @Route("/signup-sortie-{id}", name="signup")
     */
    public
    function eventSignup(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $userA = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());

        $now = new \DateTime("now");
        $now->add(new \DateInterval('PT2H'));

        $statusRepo = $this->getDoctrine()->getRepository(Status::class);
        $status = $statusRepo->getAllStatus();

        if ($event->getClosingDate() > $now and $event->getNbAttendees() < $event->getMaxAttendees()) {
            $event->addAttendee($userA);
            if ($event->getNbAttendees() == $event->getMaxAttendees()) {
                $event->setStatus($status['Closed']);
            }

            $em->flush();
            $this->addFlash('success', "Votre inscription a été prise en compte");
        } else {
            $this->addFlash('error', "Vous ne pouvez pas vous inscrire, la date limite d'inscription est dépassée ou le nombre total de participants a été atteint !");
        }

        $response = $this->forward('App\Controller\MainController::home');
        return $response;
    }

    /**
     * @Route("/signout-sortie-{id}", name="signout")
     */
    public
    function eventSignout(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $userA = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());

        $event->removeAttendee($userA);
        $em->flush();

        $response = $this->forward('App\Controller\MainController::home');
        return $response;
    }

    /**
     * @Route("/annuler-sortie-{id}", name="remove")
     */
    public
    function removeEvent(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $organiserId = $event->getOrganizer()->getId();
        $userId = $this->getUser()->getId();

        if ($organiserId == $userId) {
            $em->remove($event);
            $em->flush();
            $this->addFlash('error', "La sortie a bien été supprimée");
        } else {
            $this->addFlash('error', "Vous ne pouvez pas supprimer une sortie dont vous n'êtes pas l'organisateur");
        }
        $response = $this->forward('App\Controller\MainController::home');
        return $response;
    }


    //L'event peut-être publié si son statut

    /**
     * @Route("/publier-sortie-{id}", name="publish")
     */
    public function publishEvent(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $organiserId = $event->getOrganizer()->getId();
        $userId = $this->getUser()->getId();
        $status = new Status();

        if ($organiserId == $userId && $event->getStatus()->getLabel() == 'Creating') {

            $event->setStatus($status->setLabel('Opened'));

            $this->addFlash('error', "La sortie a bien été publiée");
        } else {
            $this->addFlash('error', "Vous ne pouvez pas publier une sortie dont vous n'êtes pas l'organisateur");
        }
        $response = $this->forward('App\Controller\MainController::home');

        return $response;
    }
}
