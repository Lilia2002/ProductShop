<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\Type\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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
}