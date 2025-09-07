<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationType;
use App\Security\LoginFormAuthenticator;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserServiceInterface $userService,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // zapis nowego usera przez UserService
            $plainPassword = $form->get('plainPassword')->getData();

            // 2. zhashuj
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

            // 3. ustaw hash w encji
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            // 4. zapisz przez UserService
            $userService->save($user);

            // 5. flash i przekierowanie na login
            $this->addFlash('success', 'Konto zostało utworzone! Zaloguj się.');
            return $this->redirectToRoute('app_login');


        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
