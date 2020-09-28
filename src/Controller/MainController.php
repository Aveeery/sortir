<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Status;
use App\Entity\User;
use App\Form\FilterEventType;
use App\Repository\EventRepository;
use DateInterval;
use DateTime;
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

        //On update les status de tous les évènements à chaque chargement de la page d'accueil
        $this->updateEventsStatus();

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


//Boucle sur tous les évènements de la base de données pour en mettre à jour le Status en fonction de la date du jour
    public function updateEventsStatus(){

        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->findAll();

        $statusRepo = $this->getDoctrine()->getRepository(Status::class);
        $status = $statusRepo->getAllStatus();

        $now = new \DateTime("now");
        $now->add(new \DateInterval('PT2H'));

        foreach ($events as $event) {

           $eventStartDate = clone $event->getStartDate();
           $eventFinished = $eventStartDate -> add(new DateInterval('PT'. $event->getDuration() .'M'));

           $eventStartDate2 = clone $event->getStartDate();
           $archiveDate = $eventStartDate2 -> add(new DateInterval('P1M'));

           if ($eventFinished < $now) {
               $event->setStatus($status['Over']);
           }

           if ($eventStartDate < $now and $eventFinished > $now) {
                $event->setStatus($status['Running']);
           }

            if ($event->getClosingDate() < $now  and $eventStartDate < $now) {
                $event->setStatus($status['Closed']);
            }

            if ($archiveDate < $now) {
                $event->setStatus($status['Archived']);
            }

            if ($event->getClosingDate() > $now and $event->getNbAttendees() < $event->getMaxAttendees())
            {
                $event->setStatus($status['Opened']);
            }
        }
    }
}
