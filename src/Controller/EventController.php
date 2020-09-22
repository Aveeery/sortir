<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/createEvent", name="create_event")
     */
    public function createEvent()
    {
        return $this->render('event/home.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }
}
