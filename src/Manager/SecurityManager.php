<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityManager
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TotpAuthenticatorInterface $totpAuthenticator
    ) {
    }

    /**
     * Get or generate temporary 2FA secret from session
     */
    public function getTempSecret(SessionInterface $session): string
    {
        $secret = $session->get('2fa_temp_secret');

        if (!$secret) {
            $secret = $this->totpAuthenticator->generateSecret();
            $session->set('2fa_temp_secret', $secret);
        }

        return $secret;
    }

    /**
     * Validate 2FA code with temporary secret
     */
    public function validateTempCode(User $user, string $code, string $tempSecret): bool
    {
        // Temporarily set the secret to validate the code
        $originalSecret = $user->getTotpAuthenticationSecret();
        $user->setTotpAuthenticationSecret($tempSecret);

        $isValid = $this->totpAuthenticator->checkCode($user, $code);

        // Restore original secret
        $user->setTotpAuthenticationSecret($originalSecret);

        return $isValid;
    }

    /**
     * Finalize 2FA activation
     */
    public function finalize2FA(User $user, string $secret, SessionInterface $session): void
    {
        $user->setTotpAuthenticationSecret($secret);
        $this->userRepository->updateUser($user);

        // Clean up session
        $session->remove('2fa_temp_secret');
    }

    /**
     * Generate QR code content with temporary secret
     */
    public function getQRCodeContentWithTempSecret(User $user, string $tempSecret): string
    {
        // Temporarily set the secret to generate QR code content
        $originalSecret = $user->getTotpAuthenticationSecret();
        $user->setTotpAuthenticationSecret($tempSecret);

        $qrContent = $this->totpAuthenticator->getQRContent($user);

        // Restore original secret
        $user->setTotpAuthenticationSecret($originalSecret);

        return $qrContent;
    }

    /**
     * Enable 2FA for a user by generating and saving the secret
     */
    public function enable2FA(User $user): void
    {
        if (!$user->getTotpAuthenticationSecret()) {
            $user->setTotpAuthenticationSecret($this->totpAuthenticator->generateSecret());
            $this->userRepository->updateUser($user);
        }
    }

    /**
     * Generate QR code content for 2FA setup
     */
    public function getQRCodeContent(User $user): string
    {
        return $this->totpAuthenticator->getQRContent($user);
    }

    /**
     * Check if user has 2FA enabled
     */
    public function has2FAEnabled(User $user): bool
    {
        return $user->getTotpAuthenticationSecret() !== null;
    }

    /**
     * Disable 2FA for a user
     */
    public function disable2FA(User $user): void
    {
        $user->setTotpAuthenticationSecret(null);
        $this->userRepository->updateUser($user);
    }
}
