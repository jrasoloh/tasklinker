<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class HomeController extends AbstractController
{
    #[Route('/dashboard', name: 'app_home')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_PROJECT_MANAGER')) {
            $projects = $user->getProjects(false);
        } else {
            $projects = $user->getNonArchivedProjects();
        }

        return $this->render('home/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
