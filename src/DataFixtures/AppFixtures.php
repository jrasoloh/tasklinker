<?php

namespace App\DataFixtures;

use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(8);

        ProjectFactory::createMany(3, function() {
            return [
                'users' => UserFactory::randomRange(2, 4)
            ];
        });

        $mainProject = ProjectFactory::createOne([
            'name' => 'TaskLinker',
            'users' => UserFactory::randomRange(3, 5)
        ]);

        TaskFactory::createMany(5, function() use ($mainProject) {
            return [
                'project' => $mainProject,
                'assignedUser' => $mainProject->getUsers()->get(
                    array_rand($mainProject->getUsers()->toArray())
                )
            ];
        });

        TaskFactory::createMany(10);
    }
}
