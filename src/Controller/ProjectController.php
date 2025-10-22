<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\ProjectType;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    #[Route('/project/new', name: 'project_create')]
    public function projectCreate(Request $request, EntityManagerInterface $em): Response
    {
        $project = new Project();
        $project->setIsArchived(false);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'Projet créé avec succès !');

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

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

    #[Route('/project/{id}/edit', name: 'project_edit')]
    public function projectEdit(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Projet mis à jour avec succès !');

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}/archive', name: 'project_archive', methods: ['POST'])]
    public function projectArchive(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('archive'.$project->getId(), $token)) {

            $project->setIsArchived(true);
            $em->flush();

            $this->addFlash('success', 'Projet archivé avec succès.');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/project/{id}/task/new', name: 'task_create')]
    public function taskCreate(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $task->setProject($project);

        $status = $request->query->get('status', 'To Do');
        $task->setStatus($status);

        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

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
        EntityManagerInterface $em
    ): Response {

        if ($task->getProject() !== $project) {
            throw $this->createNotFoundException('Tâche non trouvée dans ce projet');
        }

        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]); // [cite: 160]
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
        EntityManagerInterface $em
    ): Response {

        // On vérifie que la tâche appartient bien au projet
        if ($task->getProject() !== $project) {
            throw $this->createNotFoundException('Tâche non trouvée dans ce projet');
        }

        // On vérifie la validité du jeton CSRF
        // Le nom 'delete' . task.id doit être le même que dans le formulaire
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $token)) {
            $em->remove($task);
            $em->flush();
        }

        // On redirige vers la page du projet
        return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
    }
}
