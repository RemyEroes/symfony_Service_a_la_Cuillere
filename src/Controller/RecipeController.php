<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recipe;
use App\Entity\UserFavorite;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe_list')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        require_once __DIR__ . '/../Utils/sort_recipes_ingredients_fav.php';
        require_once __DIR__ . '/../Utils/get_ingredients_from_filters.php';
        require_once __DIR__ . '/../Utils/get_recipes_from_ingredients.php';
        require_once __DIR__ . '/../Utils/filter_infos_recipes.php';

        $requestFilters = $request->query->all();

        // sauvegarde les paramètres s'ils existent
        if (!empty($requestFilters)) {
            //check presence du filtre "ingredients"
            $filters = isset($requestFilters['ingredients']) ? $requestFilters['ingredients'] : null;

            if ($filters) {
                // decouper les filtres par "--"
                $filters_array = preg_split('/--/', $filters);


                $ingredients_array = get_ingredients_from_filters($filters_array, $entityManagerInterface);
                $filtered_recipes = get_recipes_from_ingredients($ingredients_array, $entityManagerInterface);

                // si tableau vide retourner un twig vide
                if (empty($filtered_recipes)) {
                    return $this->render('recipe/recipe-index.html.twig', [
                        'recipes' => [],
                        'filters' => true,
                    ]);
                }

                $filtered_recipes = filter_infos_recipes($filtered_recipes, $entityManagerInterface);

                // dump($filtered_recipes);

                // creation d'un tableau associatyif avec x valeurs en foncyion du nombre de filtres
                $FINAL_RECIPE_LIST = sort_recipes_by_ingredients_and_fav($filtered_recipes, $filters_array);

                // dump($FINAL_RECIPE_LIST);

                // SI FILTRES afficher les recettes filtrées
                return $this->render('recipe/recipe-index.html.twig', [
                    'recipes' => $FINAL_RECIPE_LIST,
                    'filters' => true,
                ]);
            }
        }

        // SI PAS DE FILTRES

        // Récupérer la liste des recettes complette
        $recipes_list = $entityManagerInterface->getRepository(Recipe::class)->findAll();

        // Passer la liste des recettes au template 
        return $this->render('recipe/recipe-index.html.twig', [
            'recipes' => $recipes_list,
            'filters' => false,
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

    // supprimer des favoris
    #[Route('/recette/favoris/supprimer', name: 'recipe_remove_fav')]
    public function remove_fav(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $requestparams = $request->query->all();

        $page_param = isset($requestparams['page']) ? $requestparams['page'] : null;
        $recipe_id = isset($requestparams['ids']) ? $requestparams['ids'] : null;

        if ($recipe_id) {
            $recipe_ids_array = preg_split('/--/', $recipe_id);
            $user = $this->getUser();

            foreach ($recipe_ids_array as $recipe_id) {
                delete_user_favorite_recipe($user, $recipe_id, $entityManagerInterface);
            }
        }


        if ($page_param === 'compte') {
            return $this->redirectToRoute('app_account');
        }
    }
}
