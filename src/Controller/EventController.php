<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\User;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
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
}
