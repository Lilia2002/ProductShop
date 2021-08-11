<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\Type\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\ProductType;


class AdminController extends AbstractController
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
            'form' => $form->createView(),
        ]);
    }

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

    public function productEdit($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $newName = uniqid() . $image->getClientOriginalName();
                $image->move('./uploads/product', $newName);

                $product->setImg($newName);
            }
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute("productList");
        }

        return $this->render('product/form.html.twig', [
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

    public function categoryCreate(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute("categoryEdit", [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('product/form.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    public function categoryEdit($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();

        $category = $entityManager->getRepository(Category::class)->find($id);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute("categoryList");

        }

        return $this->render('product/form.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    public function categoryList(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('product/listCategory.html.twig', [
            'category' => $categories,
        ]);
    }

    public function categoryDelete($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager = $this->getDoctrine()->getManager();

        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        $this->getUser();

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute("categoryList");
    }
}