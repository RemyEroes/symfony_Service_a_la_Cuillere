<?php


function count_common_ingredients($filters_array, $FINAL_RECIPE_LIST){
    // si filters_array > 1
    $filters_ing_value = count($filters_array) > 1 ? true : false;
    
    if ($filters_ing_value) {
        foreach ($FINAL_RECIPE_LIST as &$common_ing_recipe) {
            foreach ($common_ing_recipe as &$recipe) {
                // lowercase les ingredients
                $ingredients = $recipe['ingredients'];
                $ingredients_lower = array_map('strtolower', $ingredients);

                $common_ingr_number = 0;

                foreach ($filters_array as $filter) {
                    if (in_array(strtolower($filter), $ingredients_lower)) {
                        $common_ingr_number++;
                    }
                }

                $recipe['common_ingr_number'] = $common_ingr_number;
                $recipe['common_ingr_number_percent'] = round(($common_ingr_number / count($ingredients)) * 100);
                $recipe['number_ingr_filters'] = count($ingredients);
            }
        }
        // Libérer les références
        unset($common_ing_recipe);
        unset($recipe);
    }

    return $FINAL_RECIPE_LIST;
}