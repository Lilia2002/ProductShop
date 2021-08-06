<?php


namespace App\Controller;


use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Form\Type\BasketType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class OrderController extends AbstractController
{
    public function addProductToOrder(Request $request, $id)
    {
        $uniqueId = $request->getSession()->get('orderId');

        if (!$uniqueId) {
            $uniqueId = uniqid();
            $request->getSession()->set('orderId', $uniqueId);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $order = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        if (!$order) {
            $order = new Order();

            $datetime = new \DateTime();
            $order
                ->setCreatedAt($datetime)
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
            'order' => $order,
        ]);

        if (!$orderProduct) {
            $orderProduct = new OrderProduct();
            $orderProduct->setProduct($product);
            $order->addOrderProduct($orderProduct);
        }

        $orderProduct->setAmount($orderProduct->getAmount() + 1);
        $price = $product->getPrice();
        $order->setTotal($order->getTotal() + $price);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute("productList");
    }

    public function basketProduct(Request $request, MailerInterface $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $uniqueId = $request->getSession()->get('orderId');
        $order = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        if (!$order) {
            $this->addFlash('danger', 'You have no order yet.');
            return $this->redirectToRoute('productList');
        }

        $form = $this->createForm(BasketType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setStatus(Order::STATUS_PROCESSING);

            $entityManager->persist($order);
            $entityManager->flush();
            
            $email = new Email();

            $email
                ->from('devzimalab@gmail.com')
                ->to('liliya.p@zimalab.com')
                ->subject('Processing')
                ->html('<p>Product status - processing!</p>')
            ;

            $mailer->send($email);
        }

        return $this->render('product/listBasket.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    public function basketProductDelete($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $orderProduct = $entityManager->getRepository(OrderProduct::class)->find($id);

        $uniqueId = $request->getSession()->get('orderId');
        /** @var Order $order */
        $order = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        $total = $orderProduct->getAmount() * $orderProduct->getProduct()->getPrice();
        $order->setTotal($order->getTotal() - $total);

        $quantity = $orderProduct->getProduct()->getQuantity() + $orderProduct->getAmount();
        $orderProduct->getProduct()->setQuantity($quantity);

        $entityManager->remove($orderProduct);
        $entityManager->flush();

        return $this->redirectToRoute("basketProduct");
    }

}