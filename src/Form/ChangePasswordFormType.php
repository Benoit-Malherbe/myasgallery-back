<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => "Saisir le nouveau mot de passe",
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de saisir un nouveau mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au minimum {{ limit }} caractères',
                            'max' => 50,
                        ]),
                    ],
                    'label' => ' ',
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => "Répéter le nouveau mot de passe",
                    ],
                    'label' => ' ',
                ],
                'invalid_message' => 'Les mots de passe saisis ne sont pas identiques.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
