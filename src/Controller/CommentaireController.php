<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Recipe;
use App\Entity\Commentaire;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire/ajouter', name: 'app_commentaire_add')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        // recuperer les données du formulaire
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();


            $titre = $formData['titre'];
            $contenu = $formData['content'];
            $idRecette = $formData['recipe_id'];

            // récupérer l'utilisateur connecté
            $user = $this->getUser();

            // récupérer la recette
            $recette = $entityManagerInterface->getRepository(Recipe::class)->find($idRecette);


            // créer un nouveau commentaire
            $commentaire = new Commentaire();
            $commentaire->setTitle($titre);
            $commentaire->setCommentaire($contenu);
            $commentaire->setUser($user);
            $commentaire->setRecipe($recette);

            // enregister
            $entityManagerInterface->persist($commentaire);
            $entityManagerInterface->flush();

            // revenir à la page de la recette
            return $this->redirectToRoute('recipe_show', ['id' => $idRecette, 'slug' => $recette->getSlug()]);





        }




        // revenir à la page précédente

    }
}
