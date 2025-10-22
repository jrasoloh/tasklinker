<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TeamController extends AbstractController
{
    #[Route('/team', name: 'team_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('team/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'team_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Employé mis à jour avec succès !');

            return $this->redirectToRoute('team_index');
        }

        return $this->render('team/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'team_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em, TaskRepository $taskRepository): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $token)) {

            $tasks = $taskRepository->findBy(['assignedUser' => $user]);
            foreach ($tasks as $task) {
                $task->setAssignedUser(null);
                $em->persist($task);
            }

            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Employé supprimé avec succès.');
        }

        return $this->redirectToRoute('team_index');
    }
}
