<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\Review;
use App\Form\Type\ReviewType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends AbstractController
{
    public function productList(Request $request)
    {
        $entityManager   = $this->getDoctrine()->getManager();

        $fieldName = $request->query->get('fieldName', 'p.name');
        $direction = $request->query->get('direction', 'ASC');
        $query     = $request->query->get('query');

        $products  = $entityManager->getRepository(Product::class)->findProductsSearch($query, $fieldName, $direction);

        $uniqueId  = $request->getSession()->get('orderId');
        $order     = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);

        return $this->render('product/list.html.twig', [
            'products'       => $products,
            'order'          => $order,
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

    public function completionSearchProduct(Request $request, ProductRepository  $productRepository): JsonResponse
    {
        $query    = $request->query->get('query');
        $products = $productRepository->findProductsSearchLimit($query);

        $searchWords = [];


        foreach ($products as $product) {
            $productWords = strtolower(' ' . $product->getName() . ' ' . $product->getDescription() . ' ' . $product->getCategory()->getName());
            $matches = [];
            preg_match_all('~ ' . $query . '[a-zA-Z]* *~',$productWords, $matches);


            foreach ($matches[0] as $match) {
                $match = trim($match);
                if (strlen($match) < 3) {
                    break;
                }
                $match = preg_replace('/' . $query . '/', '<strong>'.$query.'</strong>', $match, 1);

                if (!in_array($match, $searchWords) ) {
                    $searchWords[] = $match;
                    break;
                }
            }
            if (count($searchWords) == 5) {
                break;
            }
        }

        return $this->json($searchWords);
    }

    public function product(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $review  = $entityManager->getRepository(Review::class)->findOneBy([
            'product' => $product,
            'user'    => $this->getUser(),
        ]);

        if (!$review) {
            $review = new Review();
            $form   = $this->createForm(ReviewType::class, $review);

            $review->setProduct($product);
            $review->setUser($this->getUser());

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($review);
                $entityManager->flush();
            }

            return $this->render('product/productView.html.twig', [
                'product' => $product,
                'form'    => $form->createView(),
            ]);
        }

        return $this->render('product/productView.html.twig', [
            'product' => $product,
        ]);
    }
}