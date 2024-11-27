<?php

namespace Finteger\Guest\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Flarum\Api\Controller\AbstractCreateController;
use Finteger\Guest\Serializers\GuestSerializer;
use Flarum\User\User;
use Flarum\User\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Http\SessionAuthenticator;
use Flarum\Http\SessionAccessToken;
use DateTime;
use Flarum\Http\Rememberer;
use Flarum\Http\Middleware\StartSession;
use Illuminate\Support\Arr;
use Flarum\Http\RememberAccessToken;
use Flarum\Http\AccessToken;
use Flarum\Foundation\ValidationException;
use Carbon\Carbon;

class GuestController extends AbstractCreateController
{
    public $serializer = GuestSerializer::class;

    protected $users;
    protected $sessionAuthenticator;
    /**
     * @var Rememberer
     */
    protected $rememberer;
    /**
     * @var StartSession
     */
    protected $startSession;

    public function __construct(UserRepository $users, SessionAuthenticator $sessionAuthenticator, Rememberer $rememberer, StartSession $startSession)
    {
        $this->users = $users;
        $this->sessionAuthenticator = $sessionAuthenticator;
        $this->rememberer = $rememberer;
        $this->startSession = $startSession;
    }
    

     protected function data(ServerRequestInterface $request, Document $document) 
    {
        
        // Throw an exception if the same IP makes more than 2 requests within three minutes
        $currentTime = Carbon::now();
        $ipCount = User::where('last_ip_address', $request->getAttribute('ipAddress'))
                ->where('joined_at', '>', $currentTime->subMinutes(3))
                ->count();

        if ($ipCount > 2) {
            throw new ValidationException([        'Attention' => 'Devam etmek için yeni hesap oluşturmalısın.',    ]);
        }
        
        $existingUser = User::where('last_ip_address', $request->getAttribute('ipAddress'))->first();

        // If a user with the same IP address exists, set the `$guest` variable to the existing user
        if ($existingUser) {
            
        $guest = $existingUser;
        
         } else {
             
            // If a user with the same IP address does not exist, create a new user
        $username = 'misafir' . rand(1000, 9999);
        $email = $username . '@example.com';
        $password = bin2hex(random_bytes(10));

        $existingUser = $this->users->findByEmail($email);

        if (!$existingUser) {
            $guest = User::register($username, $email, $password);
            $guest->activate();
            $guest->save();
        } else {
             $guest = $existingUser;
            }
        }

        // Generate a new token for the guest user
        $accessToken = SessionAccessToken::generate($guest->id);
        
        $validAccessToken = SessionAccessToken::findValid($accessToken);
        if ($validAccessToken) {
         $accessToken->refresh();
        }

          // Save the IP address of the newly registered user
        $updateIp = User::where('id', $guest->id)->first();
        $updateIp->last_ip_address = $request->getAttribute('ipAddress');
        $updateIp->save();
        
        
       
        $session = $request->getAttribute('session');
        $this->sessionAuthenticator->logIn($session, $accessToken);

      
       
          
        return $guest;
    }
    
    
     protected function after(ServerRequestInterface $request, Response $response, $result)
    {
   
        $this->rememberer->remember($response, $accessToken);
        
        // reload the window after logging in the guest user
        echo '<script>location.reload();</script>';
    }
    
}
