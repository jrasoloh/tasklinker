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
        UserFactory::createOne([
            'email' => 'admin@tasklinker.com',
            'firstName' => 'Pierre',
            'lastName' => 'Admin',
            'roles' => ['ROLE_PROJECT_MANAGER'],
            'status' => 'CDI'
        ]);

        UserFactory::createMany(8);

        $projects = ProjectFactory::createMany(5, function() {
            return [
                'users' => UserFactory::randomRange(2, 4)
            ];
        });

        foreach ($projects as $project) {

            TaskFactory::createMany(rand(3, 8), function() use ($project) {

                $members = $project->getUsers();

                $statuses = ['To Do', 'Doing', 'Done'];
                $status = $statuses[array_rand($statuses)];

                $assignedUser = null;

                if ($members->count() > 0) {
                    if ($status === 'To Do') {
                        $assignedUser = (rand(0, 1) === 0) ? null : $members->get(array_rand($members->toArray()));
                    } else {
                        $assignedUser = $members->get(array_rand($members->toArray()));
                    }
                } else {
                    $status = 'To Do';
                }

                return [
                    'project' => $project,
                    'status' => $status,
                    'assignedUser' => $assignedUser
                ];
            });
        }

    }
}
