<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class RecipeType extends AbstractType
{
    // récuperer l'utilisateur actuellement connecté 
    private $token;
    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('isFavorite',CheckboxType::class,[
            'attr' => [
                'class' => 'form-check-input',

            ],
            'required'  => false,
            'label' => 'Favoris ? ',
            'label_attr' => [
                'class' => 'form-check-label'
            ],
        ])
         ->add('isPublic',CheckboxType::class,[
            'attr' => [
                'class' => 'form-check-input',

            ],
            'required'  => false,
            'label' => 'Public ? ',
            'label_attr' => [
                'class' => 'form-check-label'
            ],
        ]
        )
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Nom de la recette",
                    'minlength' => '2',
                    'maxlenth' => '50'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\Length(['min' => 2, 'max' => 50]),
                    new assert\NotBlank()
                ]
            ])
      
            
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nom',
            ])
            ->add('time',IntegerType::class, [
                'attr' => [
                    'classe' => 'form-control',
                    'placeholder' => 'Temps de preparation',
                    'min' =>1,
                    'max' => 1440
                ],
                'required'  => false,
                'label' => 'Temps (en minutes)',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\LessThan(1441),
                    new assert\Positive()
                ]
            ] )
            ->add('nbPeople',IntegerType::class, [
                'attr' => [
                    'classe' => 'form-control',
                    'placeholder' => 'Nombre de personnes',
                    'min' =>1,
                    'max' => 50
                ],
                'required'  => false,
                'label' => 'Nombre de personnes',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\LessThan(51),
                    new assert\Positive()
                ]
            ] )
            ->add('difficulty', RangeType::class,[
                'attr' => [
                    'classe' => 'form-range',
                    'placeholder' => 'Niveau de difficulté',
                    'min' =>1,
                    'max' => 5
                ],
                'label' => 'Difficulté ',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\LessThan(6),
                    new assert\Positive()
                ]
            ])
      
            ->add('description',CKEditorType::class,[
                'config' => [
                    'uiColor' => '#ffffff',    
                ],
             
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "le prix en euros"

                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\Positive(),
                    new assert\LessThan(1441)
                ]
            ])
         
            ->add('imageFile',VichImageType::class,[
                'label' => 'Image de la recette',
                'required'  => false,
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]    
            ])
            ->add('ingredients',EntityType::class,[
                'class' => Ingredient::class,
                'query_builder' => function (IngredientRepository $r)
                {
                    return $r->createQueryBuilder('i')
                        ->where('i.user = :user')
                        ->orderBy('i.name', 'ASC')
                        ->setParameter('user',$this->token->getToken()->getUser());
                },
                'label' => 'Les ingrédients ',
                'label_attr' => [
                    'class' => 'form-check-label 
                     mt-4  w-25 d-flex flex-row'
                ],
                'choice_label' => 'name',
                "multiple"  => true,
                "expanded" => true,
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}