<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findNonArchivedProjectsByUser(int $userId): array
    {
        return $this->createQueryBuilder('project')
            ->join('project.users', 'user')
            ->andWhere('project.isArchived = :isArchived')
            ->andWhere('user.id = :userId')
            ->setParameter('isArchived', false)
            ->setParameter('userId', $userId)
            ->orderBy('project.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllVisibleForUser(User $user, bool $isAdmin): array
    {
        $qb = $this->createQueryBuilder('project')
            ->where('project.isArchived = :archived')
            ->setParameter('archived', false);

        if (!$isAdmin) {
            $qb->andWhere(':user MEMBER OF project.users')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }

    public function save(Project $project): void
    {
        $this->getEntityManager()->persist($project);
        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
