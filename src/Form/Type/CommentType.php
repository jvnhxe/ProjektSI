<?php
/**
 * Comment type.
 */

namespace App\Form\Type;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CategoryType.
 */
class CommentType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder Form Builder Interface
     * @param array<string, mixed> $options Options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', TextType::class, [
            'label' => 'Treść (Markdown)',
            'attr' => [
                'rows' => 8,
                'placeholder' => 'Twoja odpowiedź w Markdown (np. **pogrubienie**, _kursywa_, `kod`)',
            ],
            'constraints' => [
                new Assert\NotBlank(message: 'Treść nie może być pusta'),
            ],
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver Options Resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Comment::class]);
    }

    /**
     * @return string Comment prefix
     */
    public function getBlockPrefix(): string
    {
        return 'comment';
    }
}
