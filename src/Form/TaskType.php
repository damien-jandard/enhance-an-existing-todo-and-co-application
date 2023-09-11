<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-3',
                    'autofocus' => true
                ],
                'label' => 'Titre de la tâche',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le titre de la tâche',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le titre de la tâche doit contenir au moins {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control my-3',
                    'rows' => 5
                ],
                'label' => 'Description de la tâche',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez décrire de la tâche',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'La description de la tâche doit contenir au moins {{ limit }} caractères',
                    ]),
                ]
            ])
        ;
    }
}
