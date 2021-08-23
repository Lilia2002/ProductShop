<?php


namespace App\Form\Type;


use App\Entity\ProductSpecification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSpecificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', ChoiceType::class, [
        'choices'      => [
            'color'    => ProductSpecification::NAME_COLOR,
            'country'  => ProductSpecification::NAME_COUNTRY,
            'gender'   => ProductSpecification::NAME_GENDER,
            'material' => ProductSpecification::NAME_MATERIAL,
            'size'     => ProductSpecification::NAME_SIZE,
        ]
        ]);

        $builder
            ->add('value', TextType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductSpecification::class,
        ]);
    }
}