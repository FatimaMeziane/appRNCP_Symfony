<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Entity\Comment;
use App\Form\RecipeType;
use App\Form\CommentType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes for the  current user
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {

        $recipes = $paginator->paginate(
            //Liste des recettes de l'utilisateur connecté
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
            
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * this controller allow us to see a recipe if this one is public
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette/publique', 'recipe.index.public', methods: ['GET'])]
    public function indexPublic(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $recipes = $paginator->paginate(
            // <!-- findPublicRecipe --> une fonction que j'ai crée au niveau de recipeRepositry
            //  avec dql pour trouver les recette public ou je peut facilement la remplacer par findBy(['isPublic' => false]) 
            // $repository->findPublicRecipe(null),
            $repository->findBy(['isPublic' => false]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /** This conrtroller allow us to see a recipe if this one is public
     * @param Recipe $recipe
     * @return Response */
    #[Security("is_granted('ROLE_USER') and (recipe.isIsPublic() === true) || user === recipe.getUser()")]
    #[Route('/recette/{id}', 'recipe.show', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function show(
        Recipe $recipe,
        Request $request,
        MarkRepository $markRepository,
        EntityManagerInterface $manager
    ): Response {
        // je recherche si une note existe déjà
        $exists = true;
        $mark = $markRepository->findOneBy([
            'user' => $this->getUser(),
            'recipe' => $recipe
        ]);
        // si pas encore de vote, j'en créé un nouveau
        if (empty($mark)) {
            $mark = new Mark();
            $mark
                ->setUser($this->getUser())
                ->setRecipe($recipe);
            $exists = false;
        }
        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $markRepository->save($mark, true);
            $this->addFlash(
                'success',
                sprintf('Votre note a bien été %s', $exists ? 'mise à jour' : 'prise en compte')
            );
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }
        $comment = new Comment();
        $comment->setRecipe($recipe);
        if ($this->getUser()) {
            $comment->setAuthor($this->getUser());
        }
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);   
        
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Votre commentaire a bien été enregistré.');
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'formComment' => $formComment->createView(),
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }
    /**this controller is responsible for creating new recipe
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @return Response*/
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/nouvelleRecette', name: 'ajouteRecette', methods: ['GET', 'POST'])]
    public function newRecipe(
        Request $req,
        EntityManagerInterface $manager
    ): Response {
        /* créer une nouvelle instance de l'entité Recipe  */
        $recipe = new Recipe();
        $ingredient = [];
        /*  créer le formulaire associé */
        $form = $this->createForm(RecipeType::class, $recipe);
        /*on demande au formulaire de traiter la requête*/
        $form->handleRequest($req);
        /*on test si notre formulaire est soumise et si elle est valide*/
        if ($form->isSubmitted() && $form->isValid()) {
           /* récupérer les données soumises dans le formulaire et les assigner à l'objet $recipe*/
            $recipe = $form->getData();
            // associer l'utilisateur actuellement connecté à la nouvelle entité Recipe 
             $recipe->setUser($this->getUser());
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
    // #[IsGranted('ROLE_USER')]
    // #[Route('/recette/nouvelleRecette', 'ajouteRecette', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $manager): Response
    // {
    //     $recipe = new Recipe();
    //     $form = $this->createForm(RecipeType::class, $recipe);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $recipe = $form->getData();
    //         $recipe->setUser($this->getUser());

    //         $manager->persist($recipe);
    //         $manager->flush();

    //         $this->addFlash(
    //             'success',
    //             'Votre recette a été créé avec succès !'
    //         );

    //         return $this->redirectToRoute('recipe.index');
    //     }

    //     return $this->render('pages/recipe/new.html.twig', ['form' => $form->createview()]);
    // }
    /**
    
     * this controller is responsible for updating the recipe
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recipe/modifierRecipe/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function editRecipe(
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
                'Votre recette a été modifié avec succès!'
            );
            /* on précise la redirection*/
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', ['form' => $form->createview()]);
    }
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]

    /**
     * this controller is responsible for deleting the recipe
     */
    #[Route('/recipe/supprimerRecipe/{id}', name: 'recipe.delete', methods: ['POST'])]
    public function deleteRecipe(
        EntityManagerInterface $manager,
        Request $request,
        recipe $recipe
    ): Response {

        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès !!'
        );
    }
        return $this->redirectToRoute('recipe.index');
    }



    
    // public function delete(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    // {
    // Vérifie la validité du jeton CSRF
    // if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
    //     // Supprime la réservation de la base de données
    //     $bookingRepository->remove($booking, true);
    // }

    // Redirige vers la page d'index des réservations après la suppression
    // return $this->redirectToRoute('app_booking_delete_index', [], Response::HTTP_SEE_OTHER);
    // }


    
}