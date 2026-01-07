<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProjectRepository $projectRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_PROJECT_MANAGER')) {
            $projects = $projectRepository->findBy(['isArchived' => false]);
        }
        else {
            $projects = $user->getProjects()->filter(function(Project $project) {
                return !$project->isArchived();
            });
        }

        return $this->render('home/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
