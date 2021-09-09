<?php


namespace App\Controller;


use App\Entity\Image;
use App\Form\Type\UserType;
use App\Repository\OrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     */
    public function userOrderList(Request $request, OrderRepository $orderRepo)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $img = new Image();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $newName = uniqid() . $image->getClientOriginalName();
                $image->move('./uploads/user', $newName);

                $user->setImage($img);
                $img->setOriginalFileName($image->getClientOriginalName());
                $img->setPath('/uploads/user/' . $newName);
            }

            $entityManager->persist($img);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("userOrderList");
        }


        return $this->render('user/user.html.twig', [
            'orders' => $orderRepo->findUserOrdersSorted(
                $request->query->get('fieldName', 'o.status'),
                $request->query->get('direction', 'ASC'),
                $this->getUser()
            ),
            'form'    => $form->createView(),
        ]);
    }
}