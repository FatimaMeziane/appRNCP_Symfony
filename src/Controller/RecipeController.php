<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecipeRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Knp\Component\Pager\PaginatorInterface;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {

        $recipes = $paginator->paginate(
            $repository->findAll(), /* query NOT result */

            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    /**
     * this controller is responsible for creating new recipe
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/nouvelleRecette', name: 'ajouteRecette', methods: ['GET', 'POST'])]
    public function newRecipe(
        Request $req,
        EntityManagerInterface $manager
    ): Response {

        $recipe = new Recipe();
        /* créer la forme de type classe ingredient */
        $form = $this->createForm(RecipeType::class, $recipe);
        /*Récuperer les donnée envoye par la form via request et les affecter au objet (ingredient) auparavant donnée auparametre de la fonction create form */
        $form->handleRequest($req);
        /*on test si notre formulaire est soumise et si elle est valide*/
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            /*ensuite on fait appel au manager qui va persister la donnée et l'ajouter a la BD*/
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès!'
            );
            /* on précise la redirection*/
            return $this->redirectToRoute('recipe.index');
        }
        /* on passe la form a notre page twig pour qu'elle laffiche avec la fonction createView()*/
        return $this->render('pages/recipe/new.html.twig', ['form' => $form->createview()]);
    }

    #[Route('/recipe/modifierRecipe/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    /**
    
     * this controller is responsible for updating the recipe
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    public function editIngredient(
        Request $req,
        EntityManagerInterface $manager,
        Recipe $recipe
    ): Response {
      
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès!'
            );
            /* on précise la redirection*/
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', ['form' => $form->createview()]);
    }

    /**
     * this controller is responsible for deleting the recipe
     */
   
    #[Route('/recipe/supprimerRecipe/{id}', name: 'recipe.delete', methods: ['GET'])]
    public function deleteRecipe(
        EntityManagerInterface $manager,
        recipe $recipe
    ): Response {

        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre recette a été modifié avec succès!'
        );
        return $this->redirectToRoute('recipe.index');
    }
}
    