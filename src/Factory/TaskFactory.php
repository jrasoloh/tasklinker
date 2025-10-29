<?php

namespace App\Factory;

use App\Entity\Task;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Task::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(4),
            'description' => self::faker()->paragraph(2),
            'status' => self::faker()->randomElement(['To Do', 'Doing', 'Done']),
            'deadline' => self::faker()->optional(0.7)->dateTimeBetween('now', '+3 months'),

            'project' => ProjectFactory::random(),
            'assignedUser' => UserFactory::random(),
        ];
    }
}
