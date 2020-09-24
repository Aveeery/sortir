<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Status;
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
    public function home(Request $request)
    {

        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->findAll();

        $userId = $this->getUser()->getId();

        $filterForm = $this->createForm(FilterEventType::class);

        //Quand le formulaire en page d'accueil est soumis, on insère tous les filtres(criteria) dans un tableau pour effectuer une requête spécifique
        if ($filterForm->handleRequest($request)->isSubmitted()) {

        //Grâce aux critères de recherche récupérés (getCriteria, on peut modifier trier les events)
        $events = $eventRepo->filterEvents(
            $this->getCriteria($request, $filterForm),
            $userId);
        }

        return $this->render('main/home.html.twig', [
            "filterForm" => $filterForm->createView(), 'events' => $events
        ]);
    }

    //Range tous les critères de recherche d'events dans un tableau associatif
    public function getCriteria($request, $form)
    {
        $criteria = [];
        $fields = array_keys($form->all());

        for ($i = 0; $i < sizeof($fields); $i++) {
            $prop = $fields[$i];
            $criteria[$prop] = $form->get($prop)->getData();
        }
        return $criteria;
    }
}
