<?php

function sort_recipes_by_fav($filtered_recipes, $favorite_recipes) {


    // creation d'un tableau associatyif avec x valeurs en foncyion du nombre de filtres
    $FINAL_RECIPE_LIST = [];


    // mettre les recette favorites en pemier
    $filtered_recipes = array_map(function($recipe) use ($favorite_recipes, &$FINAL_RECIPE_LIST) {

        //check si favoris
        foreach ($favorite_recipes as $fav_recipe) {
            if ($recipe['id'] == $fav_recipe->getRecipe()->getId()) {
                $recipe['favorite'] = true;
            }
        }
        // si pas en favoris on met false et on place en dernier dans le tableau
        if (!isset($recipe['favorite'])) {
            $recipe['favorite'] = false;
            $FINAL_RECIPE_LIST[0][] = $recipe;
        }

        //  si en favoris, on place en premier dans le tableau
        if($recipe['favorite']==true) {
            $FINAL_RECIPE_LIST[1][] = $recipe;
        }

    }, $filtered_recipes);





    // retourner le tableau associatif final
    return $FINAL_RECIPE_LIST;
}
