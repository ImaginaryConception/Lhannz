<?php

namespace App\Form;

use App\Entity\PropositionConcepts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PropositionConceptsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            'data_class' => PropositionConcepts::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
