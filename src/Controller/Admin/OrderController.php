<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\Type\OrderSentType;
use App\Form\Type\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    public function orderList(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();

        $fieldName = $request->query->get('fieldName', 'o.status');
        $direction = $request->query->get('direction', 'ASC');

        $form = $this->createForm(OrderType::class);

        $form->handleRequest($request);
        $status = $form->get('Status')->getData();

        $orders = $entityManager->getRepository(Order::class)->findOrdersSorted($fieldName, $direction, $status);

        return $this->render('product/listOrders.html.twig', [
            'orders' => $orders,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function userOrderList(Request $request, OrderRepository $orderRepo)
    {
        return $this->render('product/listUserOrder.html.twig', [
            'orders' => $orderRepo->findUserOrdersSorted(
                $request->query->get('fieldName', 'o.status'),
                $request->query->get('direction', 'ASC'),
                $this->getUser()
            ),
        ]);
    }

    /**
     * есть статус - basket - статус может менять только владелец заказа на processing
     * если статус - processing - статус может менять только менеджер+ на sent or canceled
     * если статус - sent - статус может поменять только владелец заказа на completed
     * если статус - completed - менять статус нельзя
     * если статус - canceled - статус может поменять только менеджер+ на статус basket
     */

    public function changeStatusToSent(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();

        $order = $entityManager->getRepository(Order::class)->find($id);

        if ($order->getStatus() != Order::STATUS_PROCESSING) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(OrderSentType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $newName = uniqid() . $image->getClientOriginalName();
                $image->move('./uploads/product', $newName);

                $order->setImg($newName);
            }

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute("orderList");
        }

        return $this->render('product/form.html.twig', [
            'order' => $order,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     * @param string $requiredStatus
     * @param string $newStatus
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @IsGranted("ROLE_MANAGER")
     */
    public function changeOrderStatus(int $id, string $requiredStatus, string $newStatus, EntityManagerInterface $em)
    {
        $order = $em->getRepository(Order::class)->find($id);

        if ($order->getStatus() != $requiredStatus) {
            throw $this->createAccessDeniedException();
        }

        $order->setStatus($newStatus);

        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute("orderList");
    }

    public function totalSumForUsers(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager   = $this->getDoctrine()->getManager();

        $totals =  $entityManager->getRepository(Order::class)->sumTotalOrderProducts();

        $uniqueId  = $request->getSession()->get('orderId');
        $order     = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        return $this->render('product/listOrderTotal.html.twig', [
            'totals'          => $totals,
            'order'           => $order,
        ]);

    }
}