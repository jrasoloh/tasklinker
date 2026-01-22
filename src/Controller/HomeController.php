<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class HomeController extends AbstractController
{
    #[Route('/dashboard', name: 'app_home')]
    public function index(ProjectManager $projectManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $projects = $projectManager->findAllVisibleForUser(
            $user,
            $this->isGranted('ROLE_PROJECT_MANAGER')
        );

        return $this->render('home/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
