<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
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

        $user = new User();

        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);

        $event->setOrganizer($user);

        $event->setCampus($user->getCampus());

        $eventform = $this->createForm(EventType::class, $event);

        $eventform->handleRequest($request);

        if($eventform->isSubmitted() && $eventform->isValid())
        {

            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/createEvent.html.twig', [
            'controller_name' => 'EventController',
            'eventForm' => $eventform->createView(),
        ]);
    }

    /**
     * @Route("/places", name="show_places")
     */
    public function showPlaces(Request $request){
        $idCity = $request->query->get('id');
        $placeRepo = $this ->getDoctrine()->getRepository(Place::class);
        $idPlaces = $placeRepo->findAllByCity($idCity);

        return new JsonResponse(json_encode($idPlaces));
    }

    /**
     * @Route("/signup-sortie-{id}", name="signup")
     */
    public function eventSignup(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $userA = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());

        $now = new \DateTime("now");

        $now->add(new \DateInterval('PT2H'));


        if($event->getClosingDate() < $now)
        {
            $event->addAttendee($userA);
            $em->flush();
            $this->addFlash('success', "Votre inscription a été prise en compte");
        }
        else {
            $this->addFlash('error', "Vous ne pouvez pas vous inscrire, la date limite d'inscription est dépassée");
        }

        $response = $this->forward('App\Controller\MainController::home');
        return $response;


    }

    /**
     * @Route("/signout-sortie-{id}", name="signout")
     */
    public function eventSignout(Event $event)
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
    public function removeEvent(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $userA = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());

        $organiserId = $event->getOrganizer()->getId();
        $userId = $userA->getId();

        if ($organiserId == $userId)
        {

            $em->remove($event);
            $em->flush();
            $this->addFlash('error', "La sortie a bien été supprimée");
        }
        else {
            $this->addFlash('error', "Vous ne pouvez pas supprimer une sortie dont vous n'êtes pas l'organisateur");
        }

        $response = $this->forward('App\Controller\MainController::home');
        return $response;
    }
}
