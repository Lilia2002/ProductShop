<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends AbstractController
{
    public function productList(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $fieldName = $request->query->get('fieldName', 'p.name');
        $direction = $request->query->get('direction', 'ASC');
        $products = $entityManager->getRepository(Product::class)->findProductsSorted($fieldName, $direction);

        $uniqueId = $request->getSession()->get('orderId');
        $order = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'order' => $order,
        ]);
    }

    public function showPriceDynamic(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $priceHistory = $product->getPriceHistories();

        if (!$priceHistory->first()) {
            throw $this->createNotFoundException();
        }

        $price = [];
        foreach ($priceHistory as $history) {
            $price[] = $history->getPrice();
        }

        $averagePrice = array_sum($price)/count($price);

        return $this->render('product/priceDynamics.html.twig', [
            'priceHistory' => $priceHistory,
            'averagePrice' => $averagePrice,
        ]);
    }
}