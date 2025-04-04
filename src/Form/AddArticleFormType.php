<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\PropositionJeux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddArticleFormType extends AbstractType
{

    private $allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', TextType::class, [
                'label' => false,
                'empty_data' => 'Non renseigné',
                'attr' => [
                    'placeholder' => 'Prix de l\'article *',
                ],
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Le prix doit contenir au minimum {{ limit }} caractères !',
                        'max' => 2000,
                        'maxMessage' => 'Le prix ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('title', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Description du tableau',
                    'rows' => '3',
                ],
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'La description doit contenir au minimum {{ limit }} caractères !',
                        'max' => 30000,
                        'maxMessage' => 'La description ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => false,
                'choices'  => [
                    'Calligraphie' => 'Calligraphie',
                    'Abstrait' => 'Abstrait',
                    'Résine' => 'Résine',
                    'Paysage' => 'Paysage',
                    'Prénom personnalisé' => 'Prénom personnalisé',
                    'Enfant' => 'Enfant',
                    'Coque' => 'Coque',
                    'Minimaliste' => 'Minimaliste',
                    'Logo' => 'Logo',
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
            'data_class' => PropositionJeux::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
