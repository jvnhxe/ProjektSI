<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [new Assert\NotBlank(['message' => 'Email nie może być pusty'])],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,                   // ⬅️ KLUCZOWE
                'first_options'  => ['label' => 'Hasło'],
                'second_options' => ['label' => 'Powtórz hasło'],
                'invalid_message' => 'Hasła muszą być identyczne.',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Hasło nie może być puste']),
                    new Assert\Length(['min' => 6, 'minMessage' => 'Hasło musi mieć min. {{ limit }} znaków']),
                    // (opcjonalnie) new NotCompromisedPassword(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
