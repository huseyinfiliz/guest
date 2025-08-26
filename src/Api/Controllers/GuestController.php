<?php

namespace HuseyinFiliz\Guest\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Http\SessionAuthenticator;
use Flarum\Http\SessionAccessToken;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class GuestController extends AbstractCreateController
{
    public $serializer = UserSerializer::class;

    protected $settings;
    protected $sessionAuthenticator;

    public function __construct(
        SettingsRepositoryInterface $settings,
        SessionAuthenticator $sessionAuthenticator
    ) {
        $this->settings = $settings;
        $this->sessionAuthenticator = $sessionAuthenticator;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        try {
            $ip = $this->getRealClientIp($request);
            $hashedIp = $this->hashIp($ip);

            // Try to find existing guest
            $guest = User::where('last_ip_address', $hashedIp)->first();
            
            if (!$guest) {
                $guest = $this->createGuestUser($hashedIp);
            }

            // Create session
            $accessToken = SessionAccessToken::generate($guest->id);
            $accessToken->save();

            $session = $request->getAttribute('session');
            $this->sessionAuthenticator->logIn($session, $accessToken);

            return $guest;
            
        } catch (\Exception $e) {
            \Log::error('Guest login error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createGuestUser(string $hashedIp): User
    {
        $usernamePrefix = $this->settings->get('huseyinfiliz-guest.username', 'Guest');
        
        // Generate unique username
        do {
            $username = $usernamePrefix . rand(1000, 9999);
        } while (User::where('username', $username)->exists());
        
        $email = $username . '@guest.local';
        $password = bin2hex(random_bytes(16));

        $guest = User::register($username, $email, $password);
        $guest->activate();
        $guest->last_ip_address = $hashedIp;
        $guest->save();

        return $guest;
    }

    private function hashIp(string $ip): string
    {
        return hash('sha256', $ip . 'huseyinfiliz');
    }

    private function getRealClientIp(ServerRequestInterface $request): string
    {
        $serverParams = $request->getServerParams();

        if (!empty($serverParams['HTTP_CF_CONNECTING_IP'])) {
            return $serverParams['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return $serverParams['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}