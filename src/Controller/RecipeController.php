<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotNull;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe_list')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $requestFilters = $request->query->all();

        // sauvegarde les paramètres s'ils existent
        if (!empty($requestFilters)) {
            $filters = isset($requestFilters['ingredients']) ? $requestFilters['ingredients'] : null;
            if ($filters) {
                $filters_array = preg_split('/--/', $filters);
                dump($filters_array);

                foreach ($filters_array as $filter) {
                    $filter_capitalized = ucfirst($filter);
                    echo $filter_capitalized;
                    $bdd_ingredient =  $entityManagerInterface->getRepository(Ingredient::class)->findOneBy(['name' => $filter_capitalized]);

                    // ajoute si pas null
                    if ($bdd_ingredient) {
                        $ingredients_array[] = $bdd_ingredient;
                    }
                }

                // dump($ingredients_array);

                // Récupérer la liste des recettes filtrées
                foreach ($ingredients_array as $ingredient) {
                    $this_ingredient = $entityManagerInterface->getRepository(Quantity::class)->findBy(['ingredient' => $ingredient->getId()]);
                    // prednre le champ recipe_id
                    foreach ($this_ingredient as $recipe_id) {
                        $filtered_recipes_ids[] = $recipe_id->getRecipe()->getId();
                    }
                }
                
                //recette by id
                $filtered_recipes[] = $entityManagerInterface->getRepository(Recipe::class)->find($recipe_id);
                dump($filtered_recipes);
            }
        }


        // SI PAS DE FILTRES

        // Récupérer la liste des recettes complette
        $recipes_list = $entityManagerInterface->getRepository(Recipe::class)->findAll();

        // Passer la liste des recettes au template Twig
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
