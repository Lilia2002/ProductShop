<?php


namespace App\Controller;


use App\Entity\Order;
use App\Form\Type\OrderEditType;
use App\Form\Type\OrderSentType;
use App\Form\Type\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;


class ManagerController extends AbstractController
{
    public function orderList(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $fieldName = $request->query->get('fieldName', 'o.status');
        $direction = $request->query->get('direction', 'ASC');

        $form = $this->createForm(OrderType::class);

        $form->handleRequest($request);
        $status = $form->get('Status')->getData();

        if (!$status) {
            $orders = $entityManager->getRepository(Order::class)->findOrdersSorted(null, $fieldName, $direction);
        }
        else {
            $orders = $entityManager->getRepository(Order::class)->findOrdersSorted($status, $fieldName, $direction);
        }
        return $this->render('product/listOrders.html.twig', [
            'orders' => $orders,
            'form' => $form->createView(),
        ]);
    }

    public function statusEdit(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $order = $entityManager->getRepository(Order::class)->find($id);

        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute("orderList");
        }

        return $this->render('product/form.html.twig', [
            'order' => $order,
            'form'    => $form->createView(),
        ]);
    }

    public function statusSent(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $order = $entityManager->getRepository(Order::class)->find($id);

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

    public function statusCanceled(Request $request, $id)
    {

    }
}