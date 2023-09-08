<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Count;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-2',
                    'autofocus' => true
                ],
                'label' => "Nom d'utilisateur",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom d\'utilisateur',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'invalid_message' => 'Les champs de mot de passe doivent correspondre',
                'first_options' => [
                    'attr' => [
                        'class' => 'form-control my-2',
                    ],
                    'label' => 'Mot de passe'
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'form-control my-2',
                    ],
                    'label' => 'Confirmation du mot de passe'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                        'message' => 'Votre mot de passe doit comporter au minimum 8 caractères, et être composé d\'au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial (#?!@$%^&*-)'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Adresse email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse mail',
                    ]),
                    new Email([
                        'message' => 'L\'email {{ value }} n\'est pas un email valide',
                    ])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-check'
                ],
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles',
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Vous devez spécifier au moins un rôle'
                    ])
                ]
            ])
        ;
    }
}
