<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/updateprofile", name="update_profile")
     */
    //L'utilisateur peut modifier les informations de son profil
    public function updateProfile(Request $request, EntityManagerInterface $em)
    {
        $idUser = $this->getUser()->getId();
        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);

        //on conserve la valeur du password au moment ou l'utilisateur arrive sur la page
        $oldPassword = $user->getPassword();
        $profileForm = $this->createForm(UserType::class, $user);

        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {

            $this->uploadPicture($user, $profileForm);

            //Si le formulaire est soumis sans que le password ne soit changé, on insère la valeur de l'ancien password dans le nouveau
            $this->updatePassword($oldPassword, $user);
            $em->flush();

            $this->addFlash('success', 'Profil modifié');
            return $this->redirectToRoute('home');
        }

        return $this->render('user/update.html.twig', [
            "profileForm" => $profileForm->createView()
        ]);
    }


    public function uploadPicture($user, $form)
    {
        $user = $form->getData();

        $path = $this->getParameter("kernel.project_dir") . '/public/profilePictures';
        $profilePicture = $user->getProfilePicture();

        $pictureFile = $profilePicture->getFile();

        $name = md5(uniqid()) . '.' . $pictureFile->guessExtension();

        $profilePicture->setName($name);
        $pictureFile->move($path, $name);
    }

    //Si le formulaire est soumis sans que le password ne soit changé, on insère la valeur de l'ancien password dans le nouveau
    public function updatePassword($oldPassword, $user)
    {
        if (empty($user->getPassword())) {
            $user->setPassword($oldPassword);
        } else {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        }
    }

    /**
     * @Route("/profile/{id}", name="profile")
     */
    public function showProfile($id)
    {
        $profilRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $profilRepo->find($id);

        return $this->render('user/profile.html.twig', [
            "user" => $user
        ]);
    }
}
