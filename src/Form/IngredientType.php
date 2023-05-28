<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "l'ingredient",
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
                    new assert\LessThan(200)
                ]
            ]);
        /**on peut créer notre boutton a ce niveau mais si on veut
         * que notre formulaire soit réutulisable pour la modification ou autre chose
         * on rajoute le boutton au niveau de twig(l'affichage) */      
          // ->add('submit', SubmitType::class, [
        //     'attr' => [
        //         'class' => 'btn btn-primary mt-4'
        //     ],
        //     'label' => 'Créer mon ingrédient'
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}