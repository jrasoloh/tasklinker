<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public const TASK_REFERENCE = 'task';

    public function load(ObjectManager $manager): void
    {
        $task1 = new Task();
        $task1->setTitle('Développement de la page projet');
        $task1->setDescription('Page projet avec liste des tâches...');
        $task1->setStatus('Done');
        $task1->setProject($this->getReference(ProjectFixtures::PROJECT_REFERENCE, Project::class));
        $task1->setAssignedUser($this->getReference(UserFixtures::USER2_REFERENCE, User::class));
        $manager->persist($task1);

        $manager->flush();

        $this->addReference(self::TASK_REFERENCE, $task1);
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class
        ];
    }
}
