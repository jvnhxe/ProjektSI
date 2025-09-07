<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 *
 * Handles the user registration flow.
 */
class RegistrationController extends AbstractController
{
    /**
     * Displays and processes the registration form.
     *
     * When the form is submitted and valid, hashes the provided password,
     * assigns the default role, persists the user via {@see UserServiceInterface},
     * flashes a success message, and redirects to the login route.
     *
     * @param Request                     $request        Current HTTP request carrying form data.
     * @param UserServiceInterface        $userService    Application service used to persist the user.
     * @param UserPasswordHasherInterface $passwordHasher Password hasher for encoding the user's password.
     *
     * @return Response Renders the registration form or redirects to the login page on success.
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserServiceInterface $userService, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);

            $userService->save($user);

            $this->addFlash('success', 'Konto zostało utworzone! Zaloguj się.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
