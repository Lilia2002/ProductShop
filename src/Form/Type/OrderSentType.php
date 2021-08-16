<?php


namespace App\Form\Type;


use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderSentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label'    => 'Image',
                'mapped'   => false,
                'required' => false,
            ])
            ->add('sentAt', DateTimeType::class, [
                'widget' => 'choice',
            ])
            ->add('Status', ChoiceType::class, [
                'choices'        => [
                    'Sent'       => Order::STATUS_SENT,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Save'])
        ;
//
//        if ($options['data']->getDateTime()) {
//            $builder->get('dateTime')->setData(new \DateTime());
//        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}