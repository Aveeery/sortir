<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlaceController extends AbstractController
{
    /**
     * @Route("/nouveaulieu", name="create_place")
     */
    public function addPlace(Request $request, EntityManagerInterface $em)
    {
        $place = new Place();

        $form = $this->createForm(PlaceType::class, $place);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($place);
            $em->flush();
            $this->addFlash('success', 'Lieu ajouté');

            return $this->redirectToRoute('create_event');
        }

        return $this->render('place/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
