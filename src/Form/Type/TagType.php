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

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form type for managing {@see Tag} entities.
 */
class TagType extends AbstractType
{
    /**
     * Builds the form for a Tag.
     *
     * @param FormBuilderInterface $builder form builder
     * @param array<string, mixed> $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'Nazwa',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(max: 50),
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
            'data_class' => Tag::class,
        ]);
    }
}
