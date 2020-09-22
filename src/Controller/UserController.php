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
    public function updateProfile(Request $request, EntityManagerInterface $em)
    {
        $idUser = $this->getUser()->getId();

        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);
        $oldPassword = $user->getPassword();

        $profileForm = $this->createForm(UserType::class, $user);

        $profileForm->handleRequest($request);

        if($profileForm->isSubmitted() && $profileForm->isValid())
        {
            if(empty($user->getPassword())){
                $user->setPassword($oldPassword);
            } else {
                $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            }

            $em->flush();

            $this->addFlash('success', 'Profil modifié');
            return $this->redirectToRoute('home');
        }

        return $this->render('user/update.html.twig', [
            "profileForm" => $profileForm->createView()
        ]);
    }
}
