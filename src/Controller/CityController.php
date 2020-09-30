<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    /**
     * @Route("/createcity", name="create_city")
     */
    public function addCity(Request $request, EntityManagerInterface $em)
    {

        $city = new City();

        $form = $this->createForm(CityType::class, $city);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($city);
            $em->flush();
            $this->addFlash('success', 'Ville ajoutée');

            return $this->redirectToRoute('create_event');
        }

        return $this->render('city/createCity.html.twig', [
            'controller_name' => 'CityController',
            'form' => $form->createView(),
        ]);
    }
}
