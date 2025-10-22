<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROJECT_REFERENCE = 'project';

    public function load(ObjectManager $manager): void
    {
        $project1 = (new Project())
            ->setName('TaskLinker');
        $project1->addUser($this->getReference(UserFixtures::USER1_REFERENCE, User::class));
        $project1->addUser($this->getReference(UserFixtures::USER2_REFERENCE, User::class));
        $project1->addUser($this->getReference(UserFixtures::USER3_REFERENCE, User::class));
        $manager->persist($project1);

        $manager->flush();

        $this->addReference(self::PROJECT_REFERENCE, $project1);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
