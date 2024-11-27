<?php

namespace Finteger\Guest\Listeners;

use Flarum\Post\Event\Saving;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Post\Post;
use Illuminate\Support\Arr;
use Flarum\Foundation\ValidationException;

class LimitGuestPosting
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Saving::class, [$this, 'limitGuestPosting']);
    }

    public function limitGuestPosting(Saving $event)
    {
        
        $attributes = Arr::get($event->data, 'attributes', []);
        
        $post = $event->post;
        $user = $post->user;
        $username = $user->username;

        if (!preg_match('/^misafir\d{4}$/', $username)) {
            return;
        }

        $count = Post::where('user_id', $user->id)->count();
       
        
        if ($count >= 3) {
            throw new ValidationException([
                'Attention' => 'Devam etmek için yeni hesap oluştur.',
            ]);
        }
    }
}
