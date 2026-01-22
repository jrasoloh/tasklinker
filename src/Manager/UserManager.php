<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;

class UserManager
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * Fetch all users from the repository
     *
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function updateUser(User $user): void
    {
        $this->userRepository->updateUser($user);
    }
}
