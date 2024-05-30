<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {

        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer la liste des recettes favorites de l'utilisateur
        $user = $this->getUser();
        $favorite_recipes = get_favorite_recipes_from_user($user, $entityManagerInterface);
        // dump($favorite_recipes);
        $user_ingredients = get_ingredients_from_user($user, $entityManagerInterface);


        return $this->render('account/account.html.twig', [
            'favorite_recipes' => $favorite_recipes,
            'user_recipes' => $favorite_recipes,
            'user_ingredients' => $user_ingredients
        ]);
    }
}
