<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class HomeController extends AbstractController
{
    #[Route('/dashboard', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var ProjectRepository $repository */
        $repository = $entityManager->getRepository(Project::class);

        $projects = $repository->findAllVisibleForUser(
            $user,
            $this->isGranted('ROLE_PROJECT_MANAGER')
        );

        return $this->render('home/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
