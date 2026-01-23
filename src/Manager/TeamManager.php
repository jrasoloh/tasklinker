<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;

class TeamManager
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TaskRepository $taskRepository
    ) {
    }

    /**
     * Get all users
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Update a user
     */
    public function updateUser(User $user): void
    {
        $this->userRepository->updateUser($user);
    }

    /**
     * Delete a user and handle task reassignment
     */
    public function deleteUser(User $user): void
    {
        // Unassign all tasks from this user
        $tasks = $this->taskRepository->findBy(['assignedUser' => $user]);
        foreach ($tasks as $task) {
            $task->setAssignedUser(null);
        }

        // Flush task updates first
        if (!empty($tasks)) {
            $this->taskRepository->flush();
        }

        // Remove the user
        $this->userRepository->remove($user);
    }
}
