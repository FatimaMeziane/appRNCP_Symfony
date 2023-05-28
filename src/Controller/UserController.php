<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * this controller allows users to updat there profile
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(
        User $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('security.login');
        // }
        // on teste si le user recuperer avec l'Id est le même que le user courant (this) grâce au paramConverter
        // if ($this->getUser() !== $user) {
        //     return $this->redirectToRoute('recipe.index');
        // }
        $form = $this->createForm(UserType::class, $choosenUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on test si le password est valid avec 'methode isPasswordValid'
            if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {

                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées!'
                );
                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }
        return $this->render(
            'pages/user/edit.html.twig',
            ['form' => $form->createview()]
        );
    }

    /**
     * Undocumented function
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function EditPassword(
        User $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                $choosenUser->setUpdatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                    // $request->query->get('newPassword')
                );

                $this->addFlash(
                    'succes',
                    'Le mot de passe a été modifier'
                );
                $manager->persist($choosenUser);
                $manager->flush();
                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}