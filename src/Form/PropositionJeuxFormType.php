<?php

namespace App\Form;

use App\Entity\PropositionJeux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PropositionJeuxFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre du jeu *',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le titre.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères!',
                        'max' => 2000,
                        'maxMessage' => 'Le titre ne doit pas dépasser {{ limit }} caractères!',
                    ]),
                ],
            ])
            ->add('price', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prix du jeu *',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le prix.',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Le prix doit contenir au moins {{ limit }} caractères!',
                        'max' => 300,
                        'maxMessage' => 'Le prix ne doit pas dépasser {{ limit }} caractères!',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Message de la proposition *',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez remplir votre message de proposition.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le message doit contenir au moins {{ limit }} caractères!',
                        'max' => 30000,
                        'maxMessage' => 'Le message ne doit pas dépasser {{ limit }} caractères!',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'link mx-auto btn-save d-flex justify-content-center',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropositionJeux::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
