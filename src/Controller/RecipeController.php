<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe_list')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $recipes_list = $entityManagerInterface->getRepository(Recipe::class)->findAll();
        // dd($recipes);
        return $this->render('recipe/recipe-index.html.twig', [
            'recipes' => $recipes_list,
        ]);
    }


    #[Route('/recette/{slug}-{id}', name: 'recipe_show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id): Response
    {
        
        // return new Response('Recette: '. $slug);
        return $this->render('recipe/recipe-show.html.twig', [
            'slug' => $slug,
            'id' => $id
        ]);
    }
}
