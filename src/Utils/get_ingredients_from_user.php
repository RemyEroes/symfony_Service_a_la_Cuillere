<?php

use App\Entity\UserCreateIngredient;

function get_ingredients_from_user($user, $entityManagerInterface) {

    // Récupérer la liste des ingredients de l'utilisateur
    $user_ingredients = $entityManagerInterface->getRepository(UserCreateIngredient::class)->findBy(['user' => $user]);

    return $user_ingredients;

}