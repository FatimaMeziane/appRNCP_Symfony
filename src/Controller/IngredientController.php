<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\TextType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * this function display all ingredients 
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */


    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    //on donne l'accée  aux utilisateurs avec le role user
    #[IsGranted('ROLE_USER')]
    public function index(
        IngredientRepository $repository,
     PaginatorInterface $paginator, 
     Request $request): Response
    {

        $ingredients = $paginator->paginate(
            //on recupere le user depuis le token de sécurite grace a la methode getUser fournis par AbstractController
            $repository->findBy(['user' => $this->getUser()]), /* query NOT result */

            $request->query->getInt('page', 1),

            10
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * this controller is responsible for creating new ingredient
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveauIngredient', name: 'ajouterIngredient', methods: ['GET', 'POST'])]
    public function newIngredient(
        Request $req,
        EntityManagerInterface $manager
    ): Response {
        $ingredient = new Ingredient();
        /* créer la forme de type classe ingredient */
        $form = $this->createForm(IngredientType::class, $ingredient);
        /*Récuperer les donnée envoye par la form via request et les affecter au objet (ingredient) auparavant donnée auparametre de la fonction create form */
        $form->handleRequest($req);
        /*on test si notre formulaire est soumise et si elle est valide*/
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            //on relie l'ingredient a l'utilisateur connecté
            $ingredient->setUser($this->getUser());
            /*ensuite on fait appel au manager qui va persister la donnée et l'ajouter a la BD*/
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès!'
            );
            /* on précise la redirection*/
            return $this->redirectToRoute('ingredient.index');
        }

        /* on passe la form a notre page twig pour qu'elle laffiche avec la fonction createView()*/
        return $this->render('pages/ingredient/new.html.twig', ['form' => $form->createview()]);
    }
    /**
     * this controller is responsible for updating the ingredient
     */
    // on donne accee au user et on verifier que l'ingredient apartient a l'utilisateur courant 
    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredient/modifierIngredient/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function editIngredient(
        Request $req,
        EntityManagerInterface $manager,
        Ingredient $ingredient
    ): Response {
        // ici on cherche avec l'id
        // $ingredient = $repository->findOneBy(["id"=> $id]);
        // mais on a la posibilité de le faire avec le paramConverter qui nous permet retourner un objet par son id trouve dans l'url

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès!'
            );
            /* on précise la redirection*/
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/edit.html.twig', ['form' => $form->createview()]);
    }

    /**
     * this controller is responsible for deleting the ingredient
     */
    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredient/supprimerIngredient/{id}', name: 'ingredient.delete', methods: ['GET'])]
    public function deleteIngredient(
        EntityManagerInterface $manager,
        Ingredient $ingredient
    ): Response {

        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingrédient a été modifié avec succès!'
        );
        return $this->redirectToRoute('ingredient.index');
    }
}