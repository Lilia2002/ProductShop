<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\Type\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\ProductType;

class HomeController extends AbstractController
{
    public function homepage(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        return $this->render('homepage/homepage.html.twig', [
            'categories' => $categoryRepository->findCategoriesLimited(),
            'products'   => $productRepository->findProductRating(),
        ]);
    }
}