<?php namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\{
    AbstractType,
    Extension\Core\Type\EmailType,
    Extension\Core\Type\RepeatedType,
    Extension\Core\Type\TextType,
    FormBuilderInterface
};
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('password', RepeatedType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class,]);
    }
}
