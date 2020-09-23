<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\FilterEventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */

    //La page d'accueil affiche une liste d'events et un formulaire de tri pour filtrer les events à afficher
    public function home(Request $request, EventRepository $eventRepo)
    {
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->findAll();
        $userId = $this->getUser()->getId();
        $filterForm = $this->createForm(FilterEventType::class);

        //Quand le formulaire en page d'accueil est soumis, on insère tous les filtres dans un tableau pour effectuer une requête spécifique
        if ($filterForm->handleRequest($request)->isSubmitted()) {

            $criteria = [];
            $fields = array_keys($filterForm->all());

            for ($i=0 ; $i < sizeof($fields); $i++) {
                $prop = $fields[$i];
                $criteria[$prop] = $filterForm->get($prop)->getData();
            }
            $events = $eventRepo->filterEvents($criteria, $userId);
        }

        return $this->render('main/home.html.twig', [
            "filterForm" => $filterForm->createView(), 'events' => $events
        ]);
    }
}
