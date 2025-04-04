<?php

namespace App\Form;

use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DemandeFormType extends AbstractType
{

    private $allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('message', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Décrivez votre création.',
                    'rows' => '3',
                ],
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le message doit contenir au moins {{ limit }} caractères!',
                        'max' => 50000,
                        'maxMessage' => 'Le message ne doit pas dépasser {{ limit }} caractères!',
                    ]),
                ],
            ])
            ->add('images', FileType::class, array(
                'label' => false,
                'multiple' => true,
                'attr' => [
                    'accept' => implode(', ', $this->allowedMimeTypes),
                ],
            ))
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
            'data_class' => Demande::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
