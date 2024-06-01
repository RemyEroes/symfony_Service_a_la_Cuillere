<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recipe;
use App\Entity\UserFavorite;
use App\Entity\Ingredient;
use App\Entity\Measurement;
use App\Entity\Quantity;
use App\Entity\Commentaire;
use App\Entity\UserCreateRecipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\HttpKernel\KernelInterface;


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

                // si filters_array > 1
                $filters_ing_value = count($filters_array) > 1 ? true : false;// si filters_array > 1


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
    public function show(Request $request, EntityManagerInterface $entityManagerInterface, string $slug, int $id): Response
    {

        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer la recette
        $recipe = $entityManagerInterface->getRepository(Recipe::class)->find($id);

        //recuperer les ingredients
        $ingredients = $entityManagerInterface->getRepository(Quantity::class)->findBy(['recipe' => $recipe]);

        // voir si la recette est dans les favoris
        $favorites_recipes = get_favorite_recipes_from_user($this->getUser(), $entityManagerInterface);
        $is_favorite = false;

        foreach ($favorites_recipes as $favorite_recipe) {
            if ($favorite_recipe->getRecipe() == $recipe) {
                $is_favorite = true;
            }
        }

        // voir si user a créé la recette
        $user_create_recipe = $entityManagerInterface->getRepository(UserCreateRecipe::class)->findOneBy(['user' => $this->getUser(), 'recipe' => $recipe]);

        if ($user_create_recipe) {
            $is_creator = true;
        }else{
            $is_creator = false;
        }


        // prendre les commentaires
        $comments = $entityManagerInterface->getRepository(Commentaire::class)->findBy(['recipe' => $recipe]);


        // return new Response('Recette: '. $slug);
        return $this->render('recipe/recipe-show.html.twig', [
            'recipe' => $recipe,
            'ingredients' => $ingredients,
            'is_favorite' => $is_favorite,
            'is_creator' => $is_creator,
            'commentaires' => $comments
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
        if ($page_param === 'recette') {
            // page precedente
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

    }


    // ajouter aux favoris
    #[Route('/recette/favoris/ajouter', name: 'recipe_add_fav')]
    public function add_fav(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $requestparams = $request->query->all();

        $recipe_id = isset($requestparams['ids']) ? $requestparams['ids'] : null;

        if ($recipe_id) {
            $recipe_ids_array = preg_split('/--/', $recipe_id);
            $user = $this->getUser();


            foreach ($recipe_ids_array as $recipe_id) {
                $recipe = $entityManagerInterface->getRepository(Recipe::class)->find($recipe_id);

                // ajouter la recette des favoris de l'utilisateur
                $recipe_favorite = new UserFavorite();
                $recipe_favorite->setUser($user);
                $recipe_favorite->setRecipe($recipe);
                
                $entityManagerInterface->persist($recipe_favorite);
                $entityManagerInterface->flush();
            }
        }

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);

    }


    // ajouter une recette
    #[Route('/recette/ajouter', name: 'add_recipe')]
    public function add_recipe(Request $request, EntityManagerInterface $entityManagerInterface, KernelInterface $kernel): Response
    {
        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer tous les ingredients et les mesurements
        $ingredients = $entityManagerInterface->getRepository(Ingredient::class)->findAll();
        // par orde alphabétique
        usort($ingredients, function ($a, $b) {
            return $a->getName() <=> $b->getName();
        });
        $measurements = $entityManagerInterface->getRepository(Measurement::class)->findAll();


        // si le formulaire est soumis
        if ($request->isMethod('POST')) {

            $formData = $request->request->all();   
            
            $recipe = new Recipe();
            $recipe->setName($formData['nom-recette']);

            // name to slug: espaces -> "--" et caractères spéciaux en "-"
            $slug = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '-', $formData['nom-recette']));

            $recipe->setDuration($formData['temps-prep']);
            $recipe->setPeople($formData['people']);

            //get image
            $image = $request->files->get('image');
            if ($image) {
                $newFilename = move_file_and_get_filemame($image, 'recipes', $slug, $kernel);
                $recipe->setImage($newFilename);
            }

            $recipe->setSlug($slug);
            $recipe->setRecipeText($formData['recette']);

            // enregistrer la recette
            $entityManagerInterface->persist($recipe);
            $entityManagerInterface->flush();


            // enregistrer les ingredients avec les quantités
            foreach ($formData['ingredients'] as $ingredient) {
               
                // touver par nom
                $this_ingredient = $entityManagerInterface->getRepository(Ingredient::class)->findOneBy(['name' => $ingredient['name']]);
                $this_measurement = $entityManagerInterface->getRepository(Measurement::class)->findOneBy(['name' => $ingredient['measurement']]);

                $quantity = new Quantity();
                $quantity->setIngredient($this_ingredient);
                $quantity->setMeasurement($this_measurement);
                $quantity->setQuantity($ingredient['quantity']);
                $quantity->setRecipe($recipe);

                $entityManagerInterface->persist($quantity);
                $entityManagerInterface->flush();
            }

            // enregistrer qui a créé la recette
            $user_create_recipe = new UserCreateRecipe();
            $user_create_recipe->setUser($this->getUser());
            $user_create_recipe->setRecipe($recipe);

            $entityManagerInterface->persist($user_create_recipe);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('recipe_list');



        }



        return $this->render('recipe/recipe-add.html.twig',[
            'ingredients_all' => $ingredients,
            'measurements_all' => $measurements
        ]);


    }


    // supprimer une recette
    #[Route('/recette/supprimer', name: 'delete_recipe')]
    public function delete_recipe(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $requestparams = $request->query->all();

        $recipe_id = isset($requestparams['id']) ? $requestparams['id'] : null;

        if ($recipe_id) {
            $recipe = $entityManagerInterface->getRepository(Recipe::class)->find($recipe_id);

            // supprimer les ingredients
            $ingredients = $entityManagerInterface->getRepository(Quantity::class)->findBy(['recipe' => $recipe]);
            foreach ($ingredients as $ingredient) {
                $entityManagerInterface->remove($ingredient);
                $entityManagerInterface->flush();
            }

            // supprimer les favoris
            $favorites = $entityManagerInterface->getRepository(UserFavorite::class)->findBy(['recipe' => $recipe]);
            foreach ($favorites as $favorite) {
                $entityManagerInterface->remove($favorite);
                $entityManagerInterface->flush();
            }

            //supprimer qui a créé la recette
            $user_create_recipe = $entityManagerInterface->getRepository(UserCreateRecipe::class)->findOneBy(['recipe' => $recipe]);
            $entityManagerInterface->remove($user_create_recipe);
            $entityManagerInterface->flush();

            // supprimer la recette
            $entityManagerInterface->remove($recipe);
            $entityManagerInterface->flush();
        }

        return $this->redirectToRoute('recipe_list');

    }


    // modifier une recette
    #[Route('/recette/modifier/{id}', name: 'edit_recipe')]
    public function edit_recipe(Request $request, EntityManagerInterface $entityManagerInterface, int $id, KernelInterface $kernel): Response
    {
        // only authentificated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $recipe = $entityManagerInterface->getRepository(Recipe::class)->find($id);


        $recipe_ingredients = $entityManagerInterface->getRepository(Quantity::class)->findBy(['recipe' => $recipe]);

        // Récupérer tous les ingredients et les mesurements
        $ingredients = $entityManagerInterface->getRepository(Ingredient::class)->findAll();
        // par orde alphabétique
        usort($ingredients, function ($a, $b) {
            return $a->getName() <=> $b->getName();
        });
        $measurements = $entityManagerInterface->getRepository(Measurement::class)->findAll();

        // si le formulaire est soumis
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();   
            
            // Update recipe details
            $recipe->setName($formData['nom-recette']);
    
            // Convert name to slug
            $slug = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '-', $formData['nom-recette']));

            $recipe->setDuration($formData['temps-prep']);
            $recipe->setPeople($formData['people']);
    
            // Handle image upload
            $image = $request->files->get('image') ? $request->files->get('image') : null;
            if ($image) {
                $newFilename = move_file_and_get_filemame($image, 'recipes', $slug, $kernel);
                $recipe->setImage($newFilename);
            }else{
                $test = $recipe->getImage();
                $recipe->setImage($test);
            }
    
            $recipe->setSlug($slug);
            $recipe->setRecipeText($formData['recette']);

            // supprimer l'ancienne recette
            $old_recipe = $entityManagerInterface->getRepository(Recipe::class)->find($id);
            $entityManagerInterface->remove($old_recipe);
    
            // Save the updated recipe
            $entityManagerInterface->persist($recipe);
            $entityManagerInterface->flush();


            // retablir les favoris
            $favorites = $entityManagerInterface->getRepository(UserFavorite::class)->findBy(['recipe' => $old_recipe]);
            foreach ($favorites as $favorite) {
                $favorite->setRecipe($recipe);
                $entityManagerInterface->persist($favorite);
            }

    
            // Remove old ingredients
            $oldIngredients = $entityManagerInterface->getRepository(Quantity::class)->findBy(['recipe' => $recipe]);
            foreach ($oldIngredients as $ingredient) {
                $entityManagerInterface->remove($ingredient);
            }
            $entityManagerInterface->flush();
    
            // Save new ingredients with quantities
            foreach ($formData['ingredients'] as $ingredient) {
                // Find ingredient and measurement by name
                $this_ingredient = $entityManagerInterface->getRepository(Ingredient::class)->findOneBy(['name' => $ingredient['name']]);
                $this_measurement = $entityManagerInterface->getRepository(Measurement::class)->findOneBy(['name' => $ingredient['measurement']]);
    
                // Create and save new Quantity object
                $quantity = new Quantity();
                $quantity->setIngredient($this_ingredient);
                $quantity->setMeasurement($this_measurement);
                $quantity->setQuantity($ingredient['quantity']);
                $quantity->setRecipe($recipe);
    
                $entityManagerInterface->persist($quantity);
            }
            $entityManagerInterface->flush();
    
            return $this->redirectToRoute('recipe_list');
        }

        




        return $this->render('recipe/recipe-edit.html.twig',[
            'recipe' => $recipe,
            'recipe_ingredients' => $recipe_ingredients,    
            'ingredients_all' => $ingredients,
            'measurements_all' => $measurements
        ]);

    }


}
