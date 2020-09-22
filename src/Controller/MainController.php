<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
        {
            $eventRepo = $this->getDoctrine()->getRepository(Event::class);
            $events = $eventRepo->findAll();


            return $this->render('main/home.html.twig', [
                'events' => $events
            ]);
        }
}
