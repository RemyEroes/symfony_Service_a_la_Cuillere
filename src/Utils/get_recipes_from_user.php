<?php


use App\Entity\UserCreateRecipe;


function get_recipes_from_user($user, $entityManagerInterface) {

    // Récupérer la liste des recettes favorites de l'utilisateur
    $user_recipes = $entityManagerInterface->getRepository(UserCreateRecipe::class)->findBy(['user' => $user]);

    return $user_recipes;

}