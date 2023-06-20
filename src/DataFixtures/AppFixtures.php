<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Mark;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    // private UserPasswordHasherInterface $hasher;
    /** Generateur de faux donnée qu'on va passer a notre objet pour les stocker dans la base de données*/
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
        // $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        //Users
        $users = [];
        $admin = new User();
        $admin->setFullName('Administrateur de Recettes')
            ->setPseudo(null)
            ->setEmail('admin@recettes.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password');
        $user[] = $admin;
        $manager->persist($admin);
        for ($k = 0; $k < 20; $k++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setpseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');
            // on set un plainPassword quand on vas aller hasher au niveau de userListener

            // $hashPassword = $this->hasher->hashPassword(
            //     $user,
            //     'password'
            // );
            // $user->setPassword($hashPassword);
            $users[] = $user;
            $manager->persist($user);
        }
        // Ingredients
        $ingredients = [];
        for ($i = 1; $i <= 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100))
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }
        // Recipes
        $recipes = [];
        for ($j = 1; $j < 25; $j++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)])
                // ->setImageFile($this->faker->imageUrl())
                ;
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }

        //Marks
        foreach ($recipes as $recipe) {
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);
                $manager->persist($mark);
            }
        }

        //contact 
        for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande N° :' . ($i + 1))
                ->setMessage($this->faker->text());
            $manager->persist($contact);
        }

        //Comment
        foreach ($recipes as $recipe) {
            for ($i = 0; $i < mt_rand(0, 5); $i++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText)
                    ->setIsApproved(mt_rand(0, 3) === 0 ? false : true)
                    ->setAuthor($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);

                $manager->persist($comment);
                $recipe->addComment($comment);
            }
        }

        $manager->flush();
    }
}