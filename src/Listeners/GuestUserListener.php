<?php


namespace Finteger\Guest\Listeners;

use Flarum\User\Event\LoggedOut;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Http\AccessToken;

class GuestUserListener {

   public function subscribe(Dispatcher $events)
    {
         $events->listen(LoggedOut::class, [$this, 'userLoggedOut']);
    }

    public function userLoggedOut(LoggedOut $event)
    {
        // Get the user instance
        $user = $event->user;

        // Check if the user is a guest user
        if (preg_match('/^misafir\d{4}$/', $user->username)) {
         // Do something here
        }   
    }
}

