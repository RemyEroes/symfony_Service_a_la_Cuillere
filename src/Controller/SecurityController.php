<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // #[Route(path: '/register', name: 'app_register')]
    // public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    // {
    //     // $username = $request->request->get('username');
    //     // $email = $request->request->get('email');
    //     // $plainPassword = $request->request->get('password');
    
    //     // // Créez une nouvelle instance de votre entité utilisateur (ou toute autre logique de gestion des utilisateurs que vous utilisez).
    //     // $user = new User();
    //     // $user->setUsername($username);
    //     // $user->setEmail($email);
    
    //     // // Encodez le mot de passe avant de l'enregistrer en base de données.
    //     // $encodedPassword = $passwordEncoder->encodePassword($user, $plainPassword);
    //     // $user->setPassword($encodedPassword);
    
    //     // // Enregistrez l'utilisateur en base de données, vous devrez probablement ajuster cette étape selon votre logique d'application.
    //     // // Par exemple, en utilisant Doctrine ORM :
    //     // // $entityManager = $this->getDoctrine()->getManager();
    //     // // $entityManager->persist($user);
    //     // // $entityManager->flush();
    
    //     // // Redirigez l'utilisateur vers une autre page après l'inscription, par exemple la page d'accueil.
    //     // return $this->redirectToRoute('homepage');
    // }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
