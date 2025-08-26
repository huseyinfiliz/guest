<?php

namespace HuseyinFiliz\Guest;

use Flarum\Extend;
use HuseyinFiliz\Guest\Api\Controllers\GuestController;
use HuseyinFiliz\Guest\Listeners\LimitGuestPosting;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),

    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\Routes('api'))
        ->post('/guest', 'guest.create', GuestController::class),

    // Event listener - subscribe metodunu kullan
    (new Extend\Event())
        ->subscribe(LimitGuestPosting::class),
    
    (new Extend\Settings())
        ->serializeToForum('huseyinfiliz-guest.username', 'huseyinfiliz-guest.username')
        ->serializeToForum('huseyinfiliz-guest.max_posts', 'huseyinfiliz-guest.max_posts')
        ->default('huseyinfiliz-guest.username', 'Guest')
        ->default('huseyinfiliz-guest.max_posts', 3),
];