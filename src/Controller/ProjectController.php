<?php

namespace App\Controller;

use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
