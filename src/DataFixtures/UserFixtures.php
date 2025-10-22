<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER1_REFERENCE = 'user1';
    public const USER2_REFERENCE = 'user2';
    public const USER3_REFERENCE = 'user3';

    public function load(ObjectManager $manager): void
    {
        $user1 = (new User())
            ->setFirstName('Martin')
            ->setLastName('Le Loup')
            ->setEmail('martin@oc.email')
            ->setStatus('CDI')
            ->setStartDate(new \DateTime('2019-06-14'));
        $manager->persist($user1);

        $user2 = (new User())
            ->setFirstName('Huguette')
            ->setLastName('Cauchon')
            ->setEmail('huguette@oc.email')
            ->setStatus('CDD')
            ->setStartDate(new \DateTime('2021-01-10'));
        $manager->persist($user2);

        $user3 = (new User())
            ->setFirstName('Jean-Poire')
            ->setLastName('Ledève')
            ->setEmail('jpoire@oc.email')
            ->setStatus('CDI')
            ->setStartDate(new \DateTime('2020-02-17'));
        $manager->persist($user3);

        $manager->flush();

        $this->addReference(self::USER1_REFERENCE, $user1);
        $this->addReference(self::USER2_REFERENCE, $user2);
        $this->addReference(self::USER3_REFERENCE, $user3);
    }
}
