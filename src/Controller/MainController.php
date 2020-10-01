<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Status;
use App\Entity\User;
use App\Form\FilterEventType;
use App\Repository\EventRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    //La page d'accueil affiche une liste d'events et un formulaire de tri pour filtrer les events à afficher
    public function home(Request $request, PaginatorInterface $paginator)
    {
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);

        $events = $paginator->paginate(
            $eventRepo->findAllEvents(),
            $request->query->getInt('page',1), 10);

        $userId = $this->getUser()->getId();

        //On update les status de tous les évènements à chaque chargement de la page d'accueil

        $filterForm = $this->createForm(FilterEventType::class);

        //Quand le formulaire en page d'accueil est soumis, on insère tous les filtres(criteria) dans un tableau pour effectuer une requête spécifique
        if ($filterForm->handleRequest($request)->isSubmitted()) {

//            $this->updateEventsStatus();
            //Grâce aux critères de recherche récupérés (le getData du form) et l'id de session
//            $events = $eventRepo->filterEvents(
//                $filterForm->getData(),
//                $userId);
            $events = $eventRepo->filterEvents(
                    $filterForm->getData(),
                    $userId);
        }

        //On pagine les évènements grâce au knp paginator, on les affiche 9 par 9


        return $this->render('main/home.html.twig', [
            "filterForm" => $filterForm->createView(), 'events' => $events
        ]);
    }

//Boucle sur tous les évènements de la base de données pour en mettre à jour le Status en fonction de la date du jour
    /**
     * @Route("/update_status", name="update_status")
     */
    public function updateEventsStatus(){

        $em = $this->getDoctrine()->getManager();

        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->findAll();

        $statusRepo = $this->getDoctrine()->getRepository(Status::class);
        $status = $statusRepo->getAllStatus();

        $now = new \DateTime("now");
        $now->add(new \DateInterval('PT2H'));

        foreach ($events as $event) {


            //On clone la date de l'évènement pour y ajouter sa durée et définir une date de fin effective d'event
           $eventStartDate = clone $event->getStartDate();
           $eventFinished = $eventStartDate -> add(new DateInterval('PT'. $event->getDuration() .'M'));

            //On clone la date de l'évènement pour y ajouter un mois et définir une date d'archive de l'event
           $eventStartDate2 = clone $event->getStartDate();
           $archiveDate = $eventStartDate2 -> add(new DateInterval('P1M'));


            //en fonction des dates de chaque event et de son statut, on met le met à jour
           if ($eventFinished < $now and !$event->getStatus() instanceof $status['Over']) {

               $event->setStatus($status['Over']);
           }

           if ($eventStartDate < $now and $eventFinished > $now and !$event->getStatus() instanceof $status['Running']){
                $event->setStatus($status['Running']);
           }

            if ($event->getClosingDate() < $now  and $eventStartDate < $now and !$event->getStatus() instanceof $status['Over']) {
                $event->setStatus($status['Closed']);
            }

            if ($archiveDate < $now and  !$event->getStatus() instanceof $status['Archived']) {
                $event->setStatus($status['Archived']);
            }

            if ($event->getClosingDate() > $now and $event->getNbAttendees() < $event->getMaxAttendees() and  !$event->getStatus() instanceof $status['Opened'])
            {
                $event->setStatus($status['Opened']);
            }

            $em->flush();

        }
        $this->addFlash('success', 'Les statuts des sorties ont été mis à jour !');
        return $this->redirectToRoute('home');
    }
}
