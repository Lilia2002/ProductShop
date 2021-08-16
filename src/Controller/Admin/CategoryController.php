<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\Type\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
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

    public function categoryList(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('product/listCategory.html.twig', [
            'category' => $categories,
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