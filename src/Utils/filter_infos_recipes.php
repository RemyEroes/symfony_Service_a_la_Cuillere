<?php

use App\Entity\Recipe;
use App\Entity\Quantity;

function filter_infos_recipes($filtered_recipes, $entityManagerInterface) {

    // trie dans les infos de recettes
    $filtered_recipes = array_map(function(Recipe $recipe) use ($entityManagerInterface) {

        // recuperer ingredients recette
        $recipe_quantities[] = $entityManagerInterface->getRepository(Quantity::class)->findBy(['recipe' => $recipe->getId()]);
        $recipe_ingredients = [];
        foreach ($recipe_quantities as $recipe_quantity) {
            foreach ($recipe_quantity as $quantity) {
                $recipe_ingredients[] = $quantity->getIngredient()->getName();
            }
        }

        return [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'image' => $recipe->getImage(),
            'slug' => $recipe->getSlug(),
            'ingredients' => $recipe_ingredients,
        ];
    }, $filtered_recipes);


    return $filtered_recipes;

}