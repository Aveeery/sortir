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

            $criterias  ['campus'] = $filterForm->get('campus')->getData();
            $criterias  ['name'] = $filterForm->get('name')->getData();
            $criterias  ['firstDate'] = $filterForm->get('firstDate')->getData();
            $criterias  ['secondDate'] = $filterForm->get('secondDate')->getData();
            $criterias  ['organizer'] = $filterForm->get('organizer')->getData();
            $criterias  ['registered'] = $filterForm->get('registered')->getData();
            $criterias  ['notRegistered'] = $filterForm->get('notRegistered')->getData();
            $criterias  ['over'] = $filterForm->get('over')->getData();

            $events = $eventRepo->filterEvents($criterias, $userId);
        }

        return $this->render('main/home.html.twig', [
            "filterForm" => $filterForm->createView(), 'events' => $events
        ]);
    }
}
