<?php

use App\Entity\UserFavorite;

function get_favorite_recipes_from_user($user, $entityManagerInterface) {

    // Récupérer la liste des recettes favorites de l'utilisateur
    $favorite_recipes = $entityManagerInterface->getRepository(UserFavorite::class)->findBy(['user' => $user]);

    return $favorite_recipes;

}