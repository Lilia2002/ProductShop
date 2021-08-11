<?php


namespace App\Form\Type;


use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->add('dateTime', DateTimeType::class, [
                'widget' => 'choice',
            ])
            ->add('Status', ChoiceType::class, [
                'choices'        => [
                    'Basket'     => Order::STATUS_BASKET,
                    'Processing' => Order::STATUS_PROCESSING,
                    'Sent'       => Order::STATUS_SENT,
                    'Completed'  => Order::STATUS_COMPLETED,
                    'Canceled'   => Order::STATUS_CANCELED,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Save'])
        ;
    }
}