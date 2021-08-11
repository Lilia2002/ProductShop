<?php


namespace App\Form\Type;


use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('Status', ChoiceType::class, [
                'choices'        => [
                    'Basket'     => Order::STATUS_BASKET,
                    'Processing' => Order::STATUS_PROCESSING,
                    'Sent'       => Order::STATUS_SENT,
                    'Completed'  => Order::STATUS_COMPLETED,
                    'Canceled'   => Order::STATUS_CANCELED,
                    'All'        => '',
                ],
            ])
            ->add('Filter', SubmitType::class, ['label' => 'Filter'])
        ;
    }
}