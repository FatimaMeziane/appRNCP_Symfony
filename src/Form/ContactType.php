<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName',TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlenth' => '50'
                ],
                'label' => 'Nom & prénom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('email',EmailType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlenth' => '180'
                ],
                'label' => 'Adresse email',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\Length(['min' => 2, 'max' => 180]),
                    new assert\NotBlank(),
                    new assert\Email()
                ]
            ])
            ->add('subject',textType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlenth' => '100'
                ],
                'label' => 'Sujet',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\Length(['min' => 2, 'max' => 100]),
                ]
            ])
            ->add('message',TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre message"

                ],
                'label' => 'Votre message',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new assert\NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Soumettre ma demande'
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}