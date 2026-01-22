<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationManager
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * Create a new user with default values and hash password
     */
    public function createUser(string $plainPassword): User
    {
        $user = new User();

        // Set default values
        $user->setStartDate(new \DateTime());
        $user->setStatus("CDI");

        // Hash the password
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        return $user;
    }

    /**
     * Save a user to the database
     */
    public function saveUser(User $user): void
    {
        $this->userRepository->updateUser($user);
    }

    /**
     * Create and save a new user in one operation
     */
    public function registerUser(User $user, string $plainPassword): User
    {
        // Hash the password
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Set default values if not already set
        if ($user->getStartDate() === null) {
            $user->setStartDate(new \DateTime());
        }

        if ($user->getStatus() === null) {
            $user->setStatus("CDI");
        }

        // Save to database
        $this->userRepository->updateUser($user);

        return $user;
    }
}
