<?php

namespace App\Form;

use App\Entity\Planning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PlanningFormType extends AbstractType
{
    private $allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', FileType::class, [
                'label' => false,
                'data_class' => null,
                'required' => true,
                'attr' => [
                    'accept' => implode(', ', $this->allowedMimeTypes),
                    'novalidate' => 'novalidate',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '50M',
                        'mimeTypes' => $this->allowedMimeTypes,
                        'mimeTypesMessage' => 'L\'image doit être du type JPG ou PNG seulement !',
                        'maxSizeMessage' => 'Fichier trop volumineux ({{ size }} {{ suffix }}). La taille maximum autorisée est {{ limit }}{{ suffix }}',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'link mx-auto btn-save d-flex justify-content-center',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
