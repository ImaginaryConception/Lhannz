<?php

namespace App\Form;

use App\Entity\Annonce;
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

class AnnonceFormType extends AbstractType
{

    private $allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'empty_data' => 'Non spécifié',
                'attr' => [
                    'placeholder' => 'Titre de l\'annonce *',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères!',
                        'max' => 2000,
                        'maxMessage' => 'Le titre ne doit pas dépasser {{ limit }} caractères!',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => false,
                'data_class' => null,
                'attr' => [
                    'placeholder' => 'Message de l\'annonce *',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez remplir votre message d\'annonce.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le message doit contenir au moins {{ limit }} caractères!',
                        'max' => 30000,
                        'maxMessage' => 'Le message ne doit pas dépasser {{ limit }} caractères!',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'accept' => implode(', ', $this->allowedMimeTypes),
                    'novalidate' => 'novalidate',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => $this->allowedMimeTypes,
                        'mimeTypesMessage' => 'L\'image doit être du type JPG ou PNG seulement !',
                        'maxSizeMessage' => 'Fichier trop volumineux ({{ size }} {{ suffix }}). La taille maximum autorisée est {{ limit }}{{ suffix }}',
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
            'data_class' => Annonce::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
