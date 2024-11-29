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
        // İşlemi yapan kullanıcı
        $actor = $event->actor;

        // Kullanıcı ID'si 1 ile 15 arasındaysa limiti uygulama
        if ($actor->id >= 1 && $actor->id <= 99) {
            return;
        }

        // Kullanıcı adını kontrol et
        $username = $actor->username;
        if (!preg_match('/^misafir\d{4}$/', $username)) {
            return;
        }

        // Kullanıcı gönderi sayısını kontrol et
        $count = Post::where('user_id', $actor->id)->count();

        if ($count >= 3) {
            throw new ValidationException([
                'Attention' => 'Devam etmek için yeni hesap oluştur.',
            ]);
        }
    }
}
