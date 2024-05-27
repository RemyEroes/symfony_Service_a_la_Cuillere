<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Ingredient;


class IngredientController extends AbstractController
{
    #[Route('/ingredients', name: 'ingredients_list')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer la liste des ingrédients complette
        $ingredients_list = $entityManagerInterface->getRepository(Ingredient::class)->findAll();

        // Passer la liste des ingrédients au template 
        return $this->render('ingredient/ingredient-index.html.twig', [
            'ingredients' => $ingredients_list
        ]);
    }

    #[Route('/ingredient/{id}', name: 'ingredient_show')]
    public function show(int $id, EntityManagerInterface $entityManagerInterface): Response
    {
        // Only authenticated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // infos de l'ingrédient
        $ingredient = $entityManagerInterface->getRepository(Ingredient::class)->find($id);

        // récupérer les recettes dans lesquelles il y a cet ingrédient
        $recipes = get_recipes_from_ingredients([$ingredient], $entityManagerInterface);
        // dump($recipes);


        // Pass the ingredient and its recipes to the template 
        return $this->render('ingredient/ingredient-show.html.twig', [
            'ingredient' => $ingredient,
            'recipes' => $recipes
        ]);
    }

   
}
