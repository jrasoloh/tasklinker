<?php

namespace App\Factory;

use App\Entity\Project;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Project>
 */
final class ProjectFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Project::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->words(3, true) . ' Project',
            'isArchived' => false,
        ];
    }
}
