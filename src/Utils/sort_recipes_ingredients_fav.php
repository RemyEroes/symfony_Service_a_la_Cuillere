<?php

function sort_recipes_by_ingredients_and_fav($filtered_recipes, $filters_array, $favorite_recipes) {


    // creation d'un tableau associatyif avec x valeurs en foncyion du nombre de filtres
    $FINAL_RECIPE_LIST = [];


        for ($i = 1; $i <= count($filters_array); $i++) {
            $FINAL_RECIPE_LIST[$i] = [];
        }




    // mettre les recette avec les plus d'ingrédiens en commun avec les parametres en premier
    $filtered_recipes = array_map(function($recipe) use ($filters_array,$favorite_recipes, &$FINAL_RECIPE_LIST) {

        $recipe_ingredients = array_map('strtolower', $recipe['ingredients']);
        $common_ingredients = array_intersect($recipe_ingredients, $filters_array);
        $count_common_ingredients = count($common_ingredients);

        //check si favoris
        foreach ($favorite_recipes as $fav_recipe) {
            if ($recipe['id'] == $fav_recipe->getRecipe()->getId()) {
                $recipe['favorite'] = true;
            }
        }
        // si pas en favoris on met false et on place en dernier dans le tableau
        if (!isset($recipe['favorite'])) {
            $recipe['favorite'] = false;
            $FINAL_RECIPE_LIST[$count_common_ingredients][] = $recipe;
        }

        //  si en favoris, on place en premier dans le tableau
        if($recipe['favorite']==true) {
            array_unshift($FINAL_RECIPE_LIST[$count_common_ingredients], $recipe);
        }

    }, $filtered_recipes);





    // retourner le tableau associatif final
    return $FINAL_RECIPE_LIST;
}

