<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Ingredient;
use App\Entity\UserCreateIngredient;
use Symfony\Component\HttpKernel\KernelInterface;



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

    #[Route('/ingredient/{id<\d+>}', name: 'ingredient_show')]
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


    #[Route('/ingredient/ajouter', name: 'ingredient_add')]
    public function add(Request $request, EntityManagerInterface $entityManagerInterface, KernelInterface $kernel): Response
    {
        // Only authenticated users can access this page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        require_once __DIR__ . '/../Utils/move_file_and_get_filemame.php';

        if ($request->isMethod('POST')) {

            $formData = $request->request->all();
            $imageFile = $request->files->get('image-1') ? $request->files->get('image-1') : null;
            // dump($formData, $imageFile);


            $ingredient_part = 1;
            while(isset($formData['name-ingredient-' . $ingredient_part])){
               $name_ingredient = $formData['name-ingredient-'.$ingredient_part];
                if (!empty($name_ingredient)) {
                    $ingredient = new Ingredient();

                    // nom
                    $ingredient->setName($name_ingredient);

                    // nom pluriel
                    $checked_nom_pluriel = $formData['choice-'.$ingredient_part] === 'oui' ? true : false;
                    if ($checked_nom_pluriel) {
                        $ingredient->setNameMany($formData['name-pluriel-'.$ingredient_part]);
                    }


                    //image
                    $imageFile = $request->files->get('image-'.$ingredient_part);
                    //si une image, la mettre dans public/images/ingredients
                    if ($imageFile) {
                        $newFilename = move_file_and_get_filemame($imageFile, 'ingredients', $name_ingredient, $kernel);
                        $ingredient->setImage($newFilename);
                    }



                    // qui à créé l'ingrédient
                    $user_create_ing = new UserCreateIngredient();

                    $user_create_ing->setUser($this->getUser());
                    $user_create_ing->setIngredient($ingredient);
                }

                $entityManagerInterface->persist($user_create_ing);
                $entityManagerInterface->persist($ingredient);
                $entityManagerInterface->flush();

                $ingredient_part+=1;
            }

            return $this->redirectToRoute('ingredients_list');
        }

        return $this->render('ingredient/ingredient-add.html.twig');



        // $types_mesurements = [
        //     'g',
        //     'kg',
        //     'ml',
        //     'cl',
        //     'l',
        //     'cuillère à soupe',
        //     'cuillère à café',
        //     'verre',
        //     'bol',
        //     'pincée',
        //     'unité'
        // ];  
        // return $this->render('ingredient/ingredient-add.html.twig', [
        //     'types_mesurements' => $types_mesurements
        // ]);

       
    }


   
}
