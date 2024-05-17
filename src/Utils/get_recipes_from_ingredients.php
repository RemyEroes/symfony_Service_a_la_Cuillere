<?php

use App\Entity\Quantity;
use App\Entity\Recipe;


function get_recipes_from_ingredients($ingredients_array, $entityManagerInterface) {

    // Récupérer la liste des recettes filtrées
    foreach ($ingredients_array as $ingredient) {
        $this_ingredient = $entityManagerInterface->getRepository(Quantity::class)->findBy(['ingredient' => $ingredient->getId()]);
        // prednre le champ recipe_id
        
        foreach ($this_ingredient as $recipe_id) {
            $filtered_recipes_ids[] = $recipe_id->getRecipe()->getId();
            //supprimer les doublons
            $filtered_recipes_ids = array_unique($filtered_recipes_ids);
        }
    }


    // Récupérer les recettes filtrées
    foreach ($filtered_recipes_ids as $recipe_id) {
        $filtered_recipes[] = $entityManagerInterface->getRepository(Recipe::class)->find($recipe_id);
    }


    return $filtered_recipes;

}