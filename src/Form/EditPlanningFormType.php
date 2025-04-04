<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Mardi;
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

class EditPlanningFormType extends AbstractType
{

    private $allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('hour1', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Heure test *',
            ],
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir une heure.',
                ]),
                new Length([
                    'min' => 1,
                    'minMessage' => 'L\'heure doit contenir au moins {{ limit }} caractères!',
                    'max' => 300,
                    'maxMessage' => 'L\'heure ne doit pas dépasser {{ limit }} caractères!',
                ]),
            ],
        ])
            ->add('image1', FileType::class, [
                'label' => 'Image *',
                'data_class' => null,
                'required' => false,
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
            ->add('image2', FileType::class, [
                'label' => 'Image *',
                'required' => false,
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
            ->add('image3', FileType::class, [
                'label' => 'Image *',
                'required' => false,
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
                'label' => 'Confirmer',
                'attr' => [
                    'class' => 'link mx-auto btn-save d-flex justify-content-center',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mardi::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
