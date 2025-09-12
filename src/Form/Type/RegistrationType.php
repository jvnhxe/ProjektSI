<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registration form type for creating a new {@see User}.
 */
class RegistrationType extends AbstractType
{
    /**
     * Builds the registration form.
     *
     * @param FormBuilderInterface $builder form builder
     * @param array<string,mixed>  $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(message: 'Email nie może być pusty'),
                    new Assert\Email(message: 'Podaj poprawny adres email'),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'first_options'   => ['label' => 'Hasło'],
                'second_options'  => ['label' => 'Powtórz hasło'],
                'invalid_message' => 'Hasła muszą być identyczne.',
                'constraints'     => [
                    new Assert\NotBlank(message: 'Hasło nie może być puste'),
                    new Assert\Length(min: 6, minMessage: 'Hasło musi mieć min. {{ limit }} znaków'),
                ],
            ]);
    }

    /**
     * Configures the options for this form type.
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
