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
use Symfony\Component\Validator\Constraints\Length;

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

        // Récupérer les ingredients
        $ingredients_all = $entityManagerInterface->getRepository(Ingredient::class)->findAll();

        $favorite_recipes = get_favorite_recipes_from_user($this->getUser(), $entityManagerInterface);

        // sauvegarde les paramètres s'ils existent
        if (!empty($requestFilters)) {
            //check presence du filtre "ingredients"
            $filters_ingredients = isset($requestFilters['ingredients']) ? $requestFilters['ingredients'] : null;

            //check presence du filtre "name"
            $filter_name = isset($requestFilters['name']) ? $requestFilters['name'] : null;


            // si filtre nom et pas filtre ingredients  -------------------------------------------------------------------------------
            if ($filter_name && !$filters_ingredients) {
                // echo 'filtre nom';
                // remplacer "--" par " "
                $filter_name = str_replace('--', ' ', $filter_name);

                $filtered_recipes = $entityManagerInterface->getRepository(Recipe::class)->findByNameContains($filter_name);
                $filtered_recipes = filter_infos_recipes($filtered_recipes, $entityManagerInterface);

                $FINAL_RECIPE_LIST = sort_recipes_by_fav($filtered_recipes, $favorite_recipes);

                // SI FILTRES afficher les recettes filtrées
                return $this->render('recipe/recipe-index.html.twig', [
                    'recipes' => $FINAL_RECIPE_LIST,
                    'filters' => true,
                    'filters_ing' => false,
                    'ingredients_all' => $ingredients_all
                ]);
            }

            // SI FILTRE INGREDIENTS et pas nom -------------------------------------------------------------------------------
            if ($filters_ingredients && !$filter_name) {
                // echo 'filtre ingredients';

                // decouper les filtres par "--"
                $filters_array = preg_split('/--/', $filters_ingredients);


                $ingredients_array = get_ingredients_from_filters($filters_array, $entityManagerInterface);
                $filtered_recipes = get_recipes_from_ingredients($ingredients_array, $entityManagerInterface);

                // si tableau vide retourner un twig vide
                if (empty($filtered_recipes)) {
                    return $this->render('recipe/recipe-index.html.twig', [
                        'recipes' => [],
                        'filters' => true,
                        'filters_ing' => true,
                        'ingredients_all' => $ingredients_all
                    ]);
                }

                $filtered_recipes = filter_infos_recipes($filtered_recipes, $entityManagerInterface);

                // creation d'un tableau associatyif avec x valeurs en foncyion du nombre de filtres
                $FINAL_RECIPE_LIST = sort_recipes_by_ingredients_and_fav($filtered_recipes, $filters_array, $favorite_recipes);


                // si filters_array > 1
                $filters_ing_value = count($filters_array) > 1 ? true : false;
                if ($filters_ing_value) {
                    $FINAL_RECIPE_LIST = count_common_ingredients($filters_array, $FINAL_RECIPE_LIST);
                }

                // SI FILTRES afficher les recettes filtrées
                return $this->render('recipe/recipe-index.html.twig', [
                    'recipes' => $FINAL_RECIPE_LIST,
                    'filters' => true,
                    'filters_ing' => $filters_ing_value,
                    'ingredients_all' => $ingredients_all
                ]);
            }

            // SI FILTRE INGREDIENTS ET NOM  -------------------------------------------------------------------------------
            if ($filters_ingredients && $filter_name) {
                // echo 'filtre ingredients et nom';

                // decouper les filtres par "--"
                $filters_array = preg_split('/--/', $filters_ingredients);
                $filter_name = str_replace('--', ' ', $filter_name);


                $ingredients_array = get_ingredients_from_filters($filters_array, $entityManagerInterface);
                $filtered_recipes_ing = get_recipes_from_ingredients($ingredients_array, $entityManagerInterface);
                $filteres_recipes_name = $entityManagerInterface->getRepository(Recipe::class)->findByNameContains($filter_name);

                // garder ceux qui sont dans les deux tableaux
                foreach ($filtered_recipes_ing as $recipe_ing) {
                    foreach ($filteres_recipes_name as $recipe_name) {
                        if ($recipe_ing->getId() == $recipe_name->getId()) {
                            $combined_array[] = $recipe_ing;
                        }
                    }
                }


                // si tableau vide retourner un twig vide
                if (empty($combined_array) || empty($filtered_recipes_ing) || empty($filteres_recipes_name)) {
                    return $this->render('recipe/recipe-index.html.twig', [
                        'recipes' => [],
                        'filters' => true,
                        'filters_ing' => $filters_ing_value,
                        'ingredients_all' => $ingredients_all
                    ]);
                }

                $filtered_recipes = filter_infos_recipes($combined_array, $entityManagerInterface);

                // creation d'un tableau associatyif avec x valeurs en foncyion du nombre de filtres
                $FINAL_RECIPE_LIST = sort_recipes_by_ingredients_and_fav($filtered_recipes, $filters_array, $favorite_recipes);


                // si filters_array > 1
                $filters_ing_value = count($filters_array) > 1 ? true : false;
                if ($filters_ing_value) {
                    $FINAL_RECIPE_LIST = count_common_ingredients($filters_array, $FINAL_RECIPE_LIST);
                }


                // SI FILTRES afficher les recettes filtrées
                return $this->render('recipe/recipe-index.html.twig', [
                    'recipes' => $FINAL_RECIPE_LIST,
                    'filters' => true,
                    'filters_ing' => $filters_ing_value,
                    'ingredients_all' => $ingredients_all
                ]);
            }
        }


        // SI PAS DE FILTRES  -------------------------------------------------------------------------------------
        // Récupérer la liste des recettes complette
        $recipes_list = $entityManagerInterface->getRepository(Recipe::class)->findAll();
        $recipes_list = filter_infos_recipes($recipes_list, $entityManagerInterface);
        $recipes_list = sort_recipes_by_fav($recipes_list, $favorite_recipes);

        // Passer la liste des recettes au template 
        return $this->render('recipe/recipe-index.html.twig', [
            'recipes' => $recipes_list,
            'filters' => false,
            'filters_ing' => false,
            'ingredients_all' => $ingredients_all
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
