<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Review;
use App\Event\AddReviewOrChangeRatingEvent;
use App\Form\Type\ReviewType;
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
        $entityManager = $this->getDoctrine()->getManager();

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

        $orders = $entityManager->getRepository(Order::class)
            ->findOrdersOnUserAndProduct(
                $product,
                $this->getUser(),
                Order::STATUS_COMPLETED
            );

        if (!$review && $orders && $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $review = new Review();
            $form   = $this->createForm(ReviewType::class, $review);

            $review->setProduct($product);
            $review->setUser($this->getUser());

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($review);
                $entityManager->flush();
                $dispatcher->dispatch(
                    new AddReviewOrChangeRatingEvent($product),
                    AddReviewOrChangeRatingEvent::NAME
                );

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
}