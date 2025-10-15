<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setFirstName('Martin');
        $user1->setLastName('Le Loup');
        $user1->setEmail('martin@oc.email');
        $user1->setStatus('CDI');
        $user1->setStartDate(new \DateTime('2019-06-14'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setFirstName('Huguette');
        $user2->setLastName('Cauchon');
        $user2->setEmail('huguette@oc.email');
        $user2->setStatus('CDD');
        $user2->setStartDate(new \DateTime('2021-01-10'));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setFirstName('Jean-Poire');
        $user3->setLastName('Ledève');
        $user3->setEmail('jpoire@oc.email');
        $user3->setStatus('CDI');
        $user3->setStartDate(new \DateTime('2020-02-17'));
        $manager->persist($user3);

        $project1 = new Project();
        $project1->setName('TaskLinker');
        $project1->addUser($user1);
        $project1->addUser($user2);
        $project1->addUser($user3);
        $manager->persist($project1);

        $task1 = new Task();
        $task1->setTitle('Développement de la page projet');
        $task1->setDescription('Page projet avec liste des tâches...');
        $task1->setStatus('Done');
        $task1->setProject($project1);
        $task1->setAssignedUser($user2);
        $manager->persist($task1);

        $manager->flush();
    }
}
