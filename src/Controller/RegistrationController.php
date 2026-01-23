<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Manager\RegistrationManager;
use App\Security\AppAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        RegistrationManager $registrationManager,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();

        // Date du jour et Statut CDI par défaut
        $user->setStartDate(new \DateTime());
        $user->setStatus("CDI");

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $registrationManager->registerUser($user, $plainPassword);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
