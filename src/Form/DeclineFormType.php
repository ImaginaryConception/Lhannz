<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DeclineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Raison du refus',
                    'rows' => '5',
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre raison doit contenir au minimum {{ limit }} caractères !',
                        'max' => 20000,
                        'maxMessage' => 'Votre raison ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Confirmer le refus',
                'attr' => [
                    'class' => 'link mx-auto btn-save d-flex justify-content-center',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
