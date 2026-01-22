<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\ProjectType;
use App\Form\TaskType;
use App\Manager\ProjectManager;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProjectController extends AbstractController
{
    #[Route('/project/new', name: 'project_create')]
    #[IsGranted('ROLE_PROJECT_MANAGER')]
    public function projectCreate(Request $request, ProjectManager $projectManager): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectManager->createProject($project);

            $this->addFlash('success', 'Projet créé avec succès !');

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    public function show(Project $project, ProjectManager $projectManager): Response
    {
        $this->denyAccessUnlessGranted('PROJECT_VIEW', $project);

        $tasksByStatus = $projectManager->getTasksByStatus($project);

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tasksByStatus' => $tasksByStatus,
        ]);
    }

    #[Route('/project/{id}/edit', name: 'project_edit')]
    public function projectEdit(Project $project, Request $request, ProjectManager $projectManager): Response
    {
        $this->denyAccessUnlessGranted('PROJECT_EDIT', $project);

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectManager->updateProject($project);

            $this->addFlash('success', 'Projet mis à jour avec succès !');

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}/archive', name: 'project_archive', methods: ['POST'])]
    #[IsGranted('ROLE_PROJECT_MANAGER')]
    public function projectArchive(Project $project, Request $request, ProjectManager $projectManager): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('archive'.$project->getId(), $token)) {
            $projectManager->archiveProject($project);
            $this->addFlash('success', 'Projet archivé avec succès.');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/project/{id}/task/new', name: 'task_create')]
    public function taskCreate(Project $project, Request $request, ProjectManager $projectManager): Response
    {
        $this->denyAccessUnlessGranted('PROJECT_VIEW', $project);

        $task = new Task();
        $task->setProject($project);

        $status = $request->query->get('status', 'To Do');
        $task->setStatus($status);

        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectManager->createTask($task);

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('task/create.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}/task/{task_id}/edit', name: 'task_edit')]
    public function taskEdit(
        Project $project,
        #[MapEntity(id: 'task_id')] Task $task,
        Request $request,
        ProjectManager $projectManager
    ): Response {
        $this->denyAccessUnlessGranted('PROJECT_VIEW', $project);

        if ($task->getProject() !== $project) {
            throw $this->createNotFoundException('Tâche non trouvée dans ce projet');
        }

        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectManager->updateTask($task);

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'project' => $project,
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}/task/{task_id}/delete', name: 'task_delete', methods: ['POST'])]
    public function taskDelete(
        Project $project,
        #[MapEntity(id: 'task_id')] Task $task,
        Request $request,
        ProjectManager $projectManager
    ): Response {
        $this->denyAccessUnlessGranted('PROJECT_VIEW', $project);

        if ($task->getProject() !== $project) {
            throw $this->createNotFoundException('Tâche non trouvée dans ce projet');
        }

        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $token)) {
            $projectManager->deleteTask($task);
        }

        return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
    }
}
