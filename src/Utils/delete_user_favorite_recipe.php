<?php

use App\Entity\UserFavorite;
use App\Entity\Recipe;

function delete_user_favorite_recipe($user, $id_recipe, $entityManagerInterface)
{
    $recipe = $entityManagerInterface->getRepository(Recipe::class)->find($id_recipe);

    // supprimer la recette des favoris de l'utilisateur
    $recipe_favorite = $entityManagerInterface->getRepository(UserFavorite::class)->findOneBy(['user' => $user, 'recipe' => $recipe]);

    if ($recipe_favorite) {
        $entityManagerInterface->remove($recipe_favorite);
        $entityManagerInterface->flush();
    }

    return;
}
