<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Adresse e-mail *',
                ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre adresse e-mail.',
                    ]),
                    new Email([
                        'message' => 'Votre adresse e-mail ({{ value }}) n\'est pas valide!',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => false,
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Prénom *',
                ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom.',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Nom *',
                ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom.',
                    ]),
                ],
            ])
            ->add('adress', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Adresse postale *',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre adresse postale.'
                    ]),
                ],
            ])
            ->add('postalcode', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Code postal *',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre code postal.'
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Ville *',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre ville.'
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => [
                    'class' => 'link mx-auto btn-save d-flex justify-content-center',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}