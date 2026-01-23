<?php

namespace App\Manager;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;

class ProjectManager
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly TaskRepository $taskRepository
    ) {
    }

    /**
     * Create a new project
     */
    public function createProject(Project $project): Project
    {
        $project->setIsArchived(false);
        $this->projectRepository->save($project);

        return $project;
    }

    /**
     * Update an existing project
     */
    public function updateProject(Project $project): void
    {
        $this->projectRepository->flush();
    }

    /**
     * Archive a project
     */
    public function archiveProject(Project $project): void
    {
        $project->setIsArchived(true);
        $this->projectRepository->flush();
    }

    /**
     * Get tasks grouped by status for a project
     */
    public function getTasksByStatus(Project $project): array
    {
        $tasksByStatus = [
            'To Do' => [],
            'Doing' => [],
            'Done' => [],
        ];

        foreach ($project->getTasks() as $task) {
            $tasksByStatus[$task->getStatus()][] = $task;
        }

        return $tasksByStatus;
    }

    /**
     * Create a new task for a project
     */
    public function createTask(Task $task): Task
    {
        $this->taskRepository->save($task);

        return $task;
    }

    /**
     * Update an existing task
     */
    public function updateTask(Task $task): void
    {
        $this->taskRepository->flush();
    }

    /**
     * Delete a task
     */
    public function deleteTask(Task $task): void
    {
        $this->taskRepository->remove($task);
    }

    /**
     * Find all projects visible for a user based on their role
     */
    public function findAllVisibleForUser(User $user, bool $isAdmin): array
    {
        return $this->projectRepository->findAllVisibleForUser($user, $isAdmin);
    }
}
