<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\CitySearch;
use App\Form\CitySearchType;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{

    private $em;

    private $repository;

    public function __construct(CityRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/createcity", name="create_city")
     * @return Response
     */
    public function addCity(Request $request, EntityManagerInterface $em): Response
    {

        $search = new CitySearch();
        $citySearchForm = $this->createForm(CitySearchType::class, $search);
        $citySearchForm->handleRequest($request);

        $cities = $this->repository->findCityByName($search);

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
            'cities' => $cities,
            'form' => $form->createView(),
            'citySearchForm' => $citySearchForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/deletecity/{id}", requirements={"id":"\d+"}, name="admin_delete_city", methods="DELETE")
     */
    public function deleteCity(Request $request, City $city)
    {

        $this->em->remove($city);
        $this->em->flush();
        $this->addFlash('success', 'Ville supprimée avec succès');


        return $this->redirectToRoute('create_city');
    }
}
