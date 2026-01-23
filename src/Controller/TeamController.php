<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\TeamManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PROJECT_MANAGER')]
#[Route('/team')]
final class TeamController extends AbstractController
{
    #[Route('/', name: 'team_index')]
    public function index(TeamManager $teamManager): Response
    {
        $users = $teamManager->getAllUsers();

        return $this->render('team/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'team_edit')]
    public function edit(User $user, Request $request, TeamManager $teamManager): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamManager->updateUser($user);

            $this->addFlash('success', 'Employé mis à jour avec succès !');

            return $this->redirectToRoute('team_index');
        }

        return $this->render('team/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'team_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, TeamManager $teamManager): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $token)) {
            $teamManager->deleteUser($user);

            $this->addFlash('success', 'Employé supprimé avec succès.');
        }

        return $this->redirectToRoute('team_index');
    }
}
