<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    #[Route('/project/{id}', name: 'project_show')]
    public function show(Project $project): Response
    {
        $tasksByStatus = [
            'To Do' => [],
            'Doing' => [],
            'Done' => [],
        ];

        foreach ($project->getTasks() as $task) {
            $tasksByStatus[$task->getStatus()][] = $task;
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tasksByStatus' => $tasksByStatus,
        ]);
    }

    #[Route('/project/{id}/task/new', name: 'task_create')]
    public function taskCreate(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        // On associe la tâche au bon projet
        $task->setProject($project);

        // On récupère le statut depuis l'URL (ex: ?status=Doing)
        $status = $request->query->get('status', 'To Do'); // 'To Do' par défaut
        $task->setStatus($status);

        // On passe l'option 'project' au formulaire
        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            // On redirige vers la page du projet
            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('task/create.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }
}
