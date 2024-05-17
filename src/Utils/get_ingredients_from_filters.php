<?php

use App\Entity\Ingredient;

function get_ingredients_from_filters($filters_array, $entityManagerInterface ) {
    
    // pour chaque filtre, on récupère l'ingrédient correspondant
    foreach ($filters_array as $filter) {
        $filter_capitalized = ucfirst($filter);

        $bdd_ingredient =  $entityManagerInterface->getRepository(Ingredient::class)->findOneBy(['name' => $filter_capitalized]);

        // ajoute si pas null
        if ($bdd_ingredient) {
            $ingredients_array[] = $bdd_ingredient;
        }
    }

    return $ingredients_array;

};