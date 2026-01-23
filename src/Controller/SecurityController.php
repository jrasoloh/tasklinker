<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\SecurityManager;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/user/2fa/enable', name: 'app_2fa_enable')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function enable2fa(SecurityManager $securityManager, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Si la 2FA est déjà active, on redirige vers l'accueil
        if ($securityManager->has2FAEnabled($user)) {
            return $this->redirectToRoute('app_home');
        }

        // 1. Récupérer ou générer le secret temporaire depuis la session
        $session = $request->getSession();
        $tempSecret = $securityManager->getTempSecret($session);

        // 2. Gestion du formulaire (L'utilisateur a entré le code)
        if ($request->isMethod('POST')) {
            $code = $request->request->get('_auth_code');

            if ($securityManager->validateTempCode($user, $code, $tempSecret)) {
                $securityManager->finalize2FA($user, $tempSecret, $session);

                $this->addFlash('success', 'Double authentification activée avec succès !');
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('error', 'Le code est invalide. Veuillez réessayer.');
            }
        }

        // 3. Génération visuelle du QR Code (avec le secret temporaire)
        $qrCodeContent = $securityManager->getQRCodeContentWithTempSecret($user, $tempSecret);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrCodeContent)
            ->encoding(new Encoding('UTF-8'))
            ->size(200)
            ->margin(10)
            ->build();

        return $this->render('security/enable_2fa.html.twig', [
            'qrCodeImage' => $result->getDataUri()
        ]);
    }

    #[Route('/2fa', name: '2fa_login')]
    public function check2fa(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/2fa_login.html.twig', [
            'authenticationError' => $error,
            'authenticationErrorData' => $error?->getMessageData(),
            'checkPathUrl' => null,
            'checkPathRoute' => '2fa_login_check',
            'authCodeParameterName' => '_auth_code',
            'trustedParameterName' => '_trusted',
        ]);
    }
}
