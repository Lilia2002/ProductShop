<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\PriceHistory;
use App\Entity\Product;
use App\Entity\Review;
use App\Event\AddReviewOrChangeRatingEvent;
use App\Form\Type\ReviewType;
use App\Repository\OrderProductRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    public function productList(Request $request, PaginatorInterface $paginator)
    {
        $entityManager   = $this->getDoctrine()->getManager();

        $fieldName = $request->query->get('fieldName', 'p.name');
        $direction = $request->query->get('direction', 'ASC');
        $query     = $request->query->get('query');

        $products  = $entityManager->getRepository(Product::class)->findProductsSearch($query, $fieldName, $direction);

        $uniqueId  = $request->getSession()->get('orderId');
        $order     = $entityManager->getRepository(Order::class)->findOneBy(['uniqueId' => $uniqueId]);



        $products  = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('product/list.html.twig', [
            'products'       => $products,
            'order'          => $order,
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

    public function product(Request $request, $id, EventDispatcherInterface $dispatcher)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $review  = $entityManager->getRepository(Review::class)->findOneBy([
            'product' => $product,
            'user'    => $this->getUser(),
        ]);

        $orders = $entityManager->getRepository(Order::class)->findOrdersOnUserAndProduct($product, $this->getUser(), Order::STATUS_COMPLETED);

        if (!$review && $orders && $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $review = new Review();
            $form   = $this->createForm(ReviewType::class, $review);

            $review->setProduct($product);
            $review->setUser($this->getUser());

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($review);
                $entityManager->flush();
                $dispatcher->dispatch(new AddReviewOrChangeRatingEvent($product), AddReviewOrChangeRatingEvent::NAME);

                return $this->redirectToRoute("product", [
                    'id' => $product->getId(),
                ]);
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

    public function showPriceDynamic(int $id, OrderProductRepository $orderProductRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $priceHistory = $product->getPriceHistories();

        if (!$priceHistory->first()) {
            throw $this->createNotFoundException();
        }
        $orderHistory = $orderProductRepository->orderProductDynamic($product);

        $price = [];
        foreach ($priceHistory as $history) {
            $price[$history->getPrice()] = $history->getPrice();
        }

        $averagePrice = array_sum($price)/count($price);

        return $this->render('product/priceDynamics.html.twig', [
            'priceHistory' => $priceHistory,
            'averagePrice' => $averagePrice,
            'orderHistory' => $orderHistory,
        ]);
    }

    public function getDataFromTable(Request $request, OrderProductRepository $orderProductRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product_id = $request->query->get('id');

        $product = $entityManager->getRepository(Product::class)->find($product_id);

        $orderHistory = $orderProductRepository->orderProductDynamic($product);

        $order = [];
        foreach ($orderHistory as $history) {
            $order[$history['day']] = $history['amount'];
        }

        $result = [];

        foreach (new \DatePeriod(new \DateTime('-32 days'), new \DateInterval('P1D'), new \DateTime()) as $day) {
            $result[] = [
                'amount' => $order[$day->format('Y-m-d')] ?? 0,
                'day'    => $day->format('Y-m-d'),
            ];
        }
        return $this->json($result);
    }

    public function ChartPriceByDay(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product_id = $request->query->get('id');

        $product = $entityManager->getRepository(Product::class)->find($product_id);

        $priceHistory = $product->getPriceHistories();

        $price = [];
        foreach ($priceHistory as $history) {
            $price[] = [
                'day' => $history->getPriceDate()->format('Y-m-d'),
                'price' => number_format($history->getPrice() / 100, 2)
            ];
        }
        return $this->json($price);
    }
}