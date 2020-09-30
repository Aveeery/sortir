<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\User;
use App\Entity\UsersCsv;
use App\Form\UserAdminType;
use App\Form\UsersCsvType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use League\Csv\Statement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends AbstractController
{

    private $encoder;

    /**
     * @var UserRepository
     */

    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserRepository $repository, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->encoder = $encoder;
        $this->em = $em;
    }

    /**
     *
     * @Route("/admin", name="user_admin")
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        $users = $this->repository->findAll();

        $csv = new UsersCsv();

        $csvForm = $this->createForm(UsersCsvType::class, $csv);
        //$deactivateForm = $this->createForm(DeactivateUserType::class);

        $csvForm->handleRequest($request);

        if($csvForm->isSubmitted() && $csvForm->isValid())
        {
            $file = $csv->getFile();
            $fileName = md5(uniqid()).'.csv';
            $csv->setName($fileName);
            $file->move($this->getParameter('csv_dir'), $fileName);
            $csvformat = ['username', 'firstname', 'lastname', 'phone_number', 'mail', 'campus'];
            $file_path = $this->getParameter('kernel.project_dir').'/public/userCsvs/'.$fileName;

            if(($handle = fopen($file_path, 'r')) !== false) {
                if (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    for ($i = 0; $i < count($csvformat); $i++)
                    {
                        if(strcmp($data[$i], $csvformat[$i]) != 0)
                        {
                            $this->addFlash('error', "La structure du fichier n'est pas la bonne");
                            return $this->redirectToRoute('user_admin');
                        }
                    }
                }
            }

            $this->processCsv($fileName, $em);
            unlink($file_path);

            //$em->persist($csv);
            $em->flush();
            $this->addFlash('success', 'Les utilisateurs ont été ajoutés en base de donnée');

            return $this->redirectToRoute('user_admin');
        }

        $formview = $csvForm->createView();
        //$deactivateFormView = $deactivateForm->createView();
        return $this->render('user_admin/createCity.html.twig', compact('users', 'formview'));
    }

    public function processCsv($fileName, $em)
    {

        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campuses = $campusRepo->getAllCampuses();

        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir').'/public/userCsvs/'.$fileName, 'r');
        $csv->setHeaderOffset(0);


        $stmt = (new Statement())
            ->offset(0)
            ->limit(25);

        $records = $stmt->process($csv);

        foreach ($records as $record)
        {
            $user = (new User())
                ->setUsername($record['username'])
                ->setFirstname($record['firstname'])
                ->setLastname($record['lastname'])
                ->setPhoneNumber($record['phone_number'])
                ->setMail($record['mail'])
                ->setPassword('1234')
                ->setAdmin(0)
                ->setActive(1)
                ->setCampus($campuses[$record['campus']]);

            $em->persist($user);
        }
    }

    /**
     * @Route("/admin/createnewuser", name="user_admin_create_user")
     */
    public function createNewUser(Request $request, EntityManagerInterface $em)
    {
        $user = new User();

        $newUserForm = $this->createForm(UserAdminType::class, $user);

        $newUserForm->handleRequest($request);

        if ($newUserForm->isSubmitted() && $newUserForm->isValid()) {


            $user->setPassword($this->encoder->encodePassword($user, $newUserForm->getData()->getPassword()));
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur ajouté');
            return $this->redirectToRoute('user_admin');
        }

        $formview = $newUserForm->createView();
        return $this->render('user_admin/createUser.html.twig', compact( 'formview'));
    }

    /**
     * @Route("/admin/deleteusers/{id}", requirements={"id":"\d+"}, name="admin_delete_user", methods="DELETE")
     */
    public function deleteUsers(Request $request, User $user)
    {

        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succès');


        return $this->redirectToRoute('user_admin');
    }

    /**
     * @Route("/admin/deactivate/{id}", requirements={"id":"\d+"}, name="admin_deactivate_user", methods="POST")
     */
    public function deactivateUsers(Request $request, User $user)
    {

        if($user->getActive())
        {
            $user->setActive(false);
        }
        else
        {
            $user->setActive(true);
        }

        $this->em->flush();
        $this->addFlash('success', 'Utilisateur désactivé');


        return $this->redirectToRoute('user_admin');
    }
}
