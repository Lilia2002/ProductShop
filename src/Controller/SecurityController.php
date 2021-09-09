<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\Type\RegistersType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function userRegisters(Request $request, PasswordHasherFactoryInterface $hasherFactory)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();

        $form = $this->createForm(RegistersType::class, $user, ['validation_groups' => ['default', 'registration']]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordHash = $hasherFactory->getPasswordHasher($user);
            $user->setPassword($passwordHash->hash($user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("productList");
        }

        return $this->render('security/registers.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
