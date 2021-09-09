<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\OrderProductRepository;
use App\Repository\PriceHistoryRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function productCreate(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute("productEdit", [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/form.html.twig', [
            'product' => $product,
            'form'    => $form->createView(),
        ]);
    }

    public function productEdit($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $form    = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            dump($product);die;
            foreach ($product->getProductSpecifications() as $keys => $specification) {

                $name = $specification->getName();

                foreach ($product->getProductSpecifications() as $key => $value) {
                    if ($keys == $key) {
                        continue;
                    }
                    if ($value->getName() == $name) {
                        throw $this->createNotFoundException();
                    }
                }

                if ($specification->getValue() == '') {
                    throw $this->createNotFoundException();
                }
            }

//            $entityManager->persist($img);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute("productList");
        }

        return $this->render('product/editForm.html.twig', [
            'product' => $product,
            'form'    => $form->createView(),
        ]);
    }

    public function productDelete($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute("productList");
    }

    public function productListAdmin(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

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

        return $this->render('product/listAdmin.html.twig', [
            'products'       => $products,
            'order'          => $order,
        ]);
    }

    public function productStatistics(int $id, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $product = $productRepository->find($id);

//        $averagePrice = array_sum($price)/count($price); todo: придумать где и как отображать среднюю стоимость продукта

        return $this->render('product/priceDynamics.html.twig', [
            'product' => $product,
        ]);
    }

    public function getDataProductOrderStatistics(Request $request, OrderProductRepository $orderProductRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $productId = $request->query->get('id');
        $orderHistory = $orderProductRepository->orderProductDynamic($productId);
        $startDate = new \DateTime($request->query->get('start') ?: '-31 days');
        $endDate   = new \DateTime($request->query->get('end') ?: 'now');

        $order = [];
        foreach ($orderHistory as $history) {
            $order[$history['day']] = $history['amount'];
        }

        $result = [];

        foreach (new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate) as $day) {
            $result[] = [
                'y' => $order[$day->format('Y-m-d')] ?? 0,
                'x' => $day->format('Y-m-d'),
            ];
        }
        return $this->json($result);
    }

    public function getDataProductPriceStatistics(Request $request, PriceHistoryRepository  $priceHistoryRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $startDate = new \DateTime($request->query->get('start') ?: '-31 days');
        $endDate   = new \DateTime($request->query->get('end') ?: 'now');

        $pricesHistory = $priceHistoryRepository->findPricesInRange($request->query->get('id'));
        $prices = [];

        foreach ($pricesHistory as $priceHistory) {
            $prices[$priceHistory->getPriceDate()->format('Y-m-d')] = $priceHistory->getPrice();
        }

        $priceStartDate = 0;
        $pricesReverse  = array_reverse($prices);
        foreach ($pricesReverse as $date => $value) {
            if ($startDate->format('Y-m-d') > $date) {
                $priceStartDate = $value;
                break;
            }
        }

        $result = [];
        $result[$startDate->format('Y-m-d')] = $priceStartDate;

        foreach (new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate) as $day) {
            $currentDate  = $day->format('Y-m-d');
            $previousDate = (clone $day)->modify('-1 day')->format('Y-m-d');

            if (array_key_exists($currentDate, $prices)) {
                $result[$currentDate] = $prices[$currentDate];
            } elseif ($currentDate   !== $startDate->format('Y-m-d')) {
                $result[$currentDate] = $result[$previousDate];
            }
        }

        $price = [];

        foreach (new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate) as $day) {
            $price[] = [
                'y' => number_format($result[$day->format('Y-m-d')] / 100, 2),
                'x' => $day->format('Y-m-d'),
            ];
        }

        return $this->json($price);
    }

    public function imageLoading(Request $request)
    {
        $file = $request->files->get('attachment');

        if (!$file) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        $originalName = $file->getClientOriginalName();
        $filename = uniqid() . $file->getClientOriginalName();
        $path = "./uploads/product/";
        $file->move($path, $filename);

        return $this->json([
            'path' => $path . $filename,
            'originalFileName' => $originalName,
        ]);
    }
}