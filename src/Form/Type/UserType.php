<?php


namespace App\Form\Type;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('age', TextType::class)
            ->add('gender', ChoiceType::class, [
                'choices'             => [
                    'Women'      => User::GENDER_WOMEN,
                    'Men'        => User::GENDER_MEN,
                ],
            ])
            ->add('address', TextType::class)
            ->add('image', FileType::class, [
                'label'    => 'Image',
                'mapped'   => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Save'])
        ;
    }
}