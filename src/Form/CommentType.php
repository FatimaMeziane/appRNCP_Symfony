<?php
namespace App\Form; use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

 class CommentType extends AbstractType{ 

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('content',TextareaType::class, [
            'label' => 'Poster un nouveau commentaire',
            'constraints' => [
                new assert\NotBlank()
            ]
       ])
       ->add('submit', SubmitType::class, [
        'attr' => ['class' => 'btn btn-primary mt-4'],
        'label' => 'Poster mon commentaire'
    ]);
    }
    }