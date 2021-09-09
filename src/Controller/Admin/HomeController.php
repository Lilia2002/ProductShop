<?php


namespace App\Controller\Admin;


use App\Entity\Order;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function orderStatistics(OrderRepository $orderRepository): Response
    {
        return $this->render('homepage/homepageAdmin.html.twig');
    }

    public function getDataOrderStatistics(Request $request, OrderRepository $orderRepository)
    {
        $startDate = new \DateTime($request->query->get('start') ?: '-31 days');
        $endDate   = new \DateTime($request->query->get('end') ?: '+1 day');

        $orders = $orderRepository->ordersDynamic(Order::STATUS_PROCESSING);

        $orderHistory = [];
        foreach ($orders as $history) {
            $orderHistory[$history['day']] = $history['id'];
        }

        $result = [];
        foreach (new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate) as $day) {
            $result[] = [
                'y' => $orderHistory[$day->format('Y-m-d')] ?? 0,
                'x' => $day->format('Y-m-d'),
            ];
        }

        return $this->json($result);
    }

    public function getDataOrderTotalStatistics(Request $request, OrderRepository $orderRepository)
    {
        $startDate = new \DateTime($request->query->get('start') ?: '-31 days');
        $endDate   = new \DateTime($request->query->get('end') ?: '+1 day');

        $orders = $orderRepository->ordersTotalDynamic(Order::STATUS_PROCESSING);

        $orderHistory = [];
        foreach ($orders as $history) {
            $orderHistory[$history['day']] = $history['total'];
        }

        $result = [];
        foreach (new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate) as $day) {
            $result[] = [
                'y' => number_format(($orderHistory[$day->format('Y-m-d')] ?? 0) / 100, 2),
                'x' => $day->format('Y-m-d'),
            ];
        }

        return $this->json($result);
    }
}