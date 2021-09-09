<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Form\Type\BasketType;
use App\Service\EmailService;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class OrderController extends AbstractController
{

    public function addProductToOrder(Request $request, $id, OrderService $service)
    {
        $uniqueId = $request->getSession()->get('orderId');

        if (!$uniqueId) {
            $uniqueId = uniqid();
            $request->getSession()->set('orderId', $uniqueId);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $order = $entityManager->getRepository(Order::class)->findOrderWithOrderProducts($uniqueId);

        if (!$order) {
            $order = new Order();

            $order
                ->setUser($this->getUser())
                ->setStatus(Order::STATUS_BASKET)
                ->setUniqueId($uniqueId)
            ;
        }

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product || !$product->getQuantity()) {
            $this->addFlash(
                'warning',
                'Продукт отсутствует на складе.'
            );
           return $this->redirectToRoute("productList");
        }

        $product->setQuantity($product->getQuantity() - 1);

        $orderProduct = $entityManager->getRepository(OrderProduct::class)->findOneBy([
            'product' => $product,
            'order'   => $order,
        ]);

        if (!$orderProduct) {
            $orderProduct = new OrderProduct();
            $orderProduct->setProduct($product);
            $order->addOrderProduct($orderProduct);
        }

        $orderProduct->setAmount($orderProduct->getAmount() + 1);

        $service->calculateOrderTotal($order);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute("productList");
    }

    public function viewOrder(Request $request, EmailService $service)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $uniqueId = $request->getSession()->get('orderId');
        $order    = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        if (!$order) {
            $this->addFlash('danger', 'You have no order yet.');
            return $this->redirectToRoute('productList');
        }

        $form = $this->createForm(BasketType::class, $order);

        $form->handleRequest($request); // handle - обработать

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$order->getUser()) {
                $this->addFlash(
                    'warning',
                    'Необходимо зарегистрироваться, чтобы оформить заказ!'
                );
                return $this->redirectToRoute("userRegisters");
            }

            $order->setStatus(Order::STATUS_PROCESSING);
            $order->setProcessedAt(new \DateTime());

            $entityManager->persist($order);
            $entityManager->flush();
            
            $service->sendOrderStatusChangedEmail('liliya.p@zimalab.com', 'processing');
        }

        return $this->render('order/listBasket.html.twig', [
            'order' => $order,
            'form'  => $form->createView(),
        ]);
    }

    public function deleteProductFromOrder($id, Request $request, OrderService $service)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $orderProduct  = $entityManager->getRepository(OrderProduct::class)->find($id);

        $uniqueId = $request->getSession()->get('orderId');
        /** @var Order $order */
        $order = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        $quantity = $orderProduct->getProduct()->getQuantity() + $orderProduct->getAmount();
        $orderProduct->getProduct()->setQuantity($quantity);

        $entityManager->remove($orderProduct);
        $entityManager->flush();

        $service->calculateOrderTotal($order);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute("basketProduct");
    }

    public function changeStatusToCompleted(int $id)
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