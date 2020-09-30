<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\CampusSearch;
use App\Form\CampusSearchType;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CampusAdminController extends AbstractController
{

    private $em;

    private $repository;

    public function __construct(CampusRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }
    /**
     * @Route("/campus/admin", name="campus_admin")
     */
    public function addCampus(Request $request, EntityManagerInterface $em)
    {
        $search = new CampusSearch();
        $campusSearchForm = $this->createForm(CampusSearchType::class, $search);
        $campusSearchForm->handleRequest($request);

        $campuses = $this->repository->findCampusByName($search);

        $campus = new Campus();

        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($campus);
            $em->flush();
            $this->addFlash('success', 'Campus ajouté avec succès');

            return $this->redirectToRoute('campus_admin');
        }



        return $this->render('campus_admin/createCampus.html.twig', [
            'controller_name' => 'CampusAdminController',
            'campuses' => $campuses,
            'form' => $form->createView(),
            'campusSearchForm' => $campusSearchForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/deletecampus/{id}", requirements={"id":"\d+"}, name="admin_delete_campus", methods="DELETE")
     */
    public function deleteCity(Request $request, Campus $campus)
    {

        $this->em->remove($campus);
        $this->em->flush();
        $this->addFlash('success', 'Campus supprimé avec succès');


        return $this->redirectToRoute('campus_admin');
    }
}
