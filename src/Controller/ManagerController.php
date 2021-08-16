<?php


namespace App\Controller;


use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ManagerController extends AbstractController
{
    public function statusCompleted(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $order = $entityManager->getRepository(Order::class)->find($id);

        if ($this->getUser() != $order->getUser() || $order->getStatus() != Order::STATUS_SENT) {
            throw $this->createAccessDeniedException();
        }

        $order->setStatus(Order::STATUS_COMPLETED);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute("orderList");
    }
}