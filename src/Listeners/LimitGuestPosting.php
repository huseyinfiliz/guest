<?php

namespace HuseyinFiliz\Guest\Listeners;

use Flarum\Post\Event\Saving;
use Flarum\Post\Post;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class LimitGuestPosting
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(Saving::class, [$this, 'handle']);
    }

    public function handle(Saving $event)
    {
        $actor = $event->actor;
        
        // Skip if not logged in or is admin
        if (!$actor->exists || $actor->isAdmin()) {
            return;
        }

        // Get settings
        $usernamePrefix = $this->settings->get('huseyinfiliz-guest.username', 'Guest');
        $maxPosts = (int) $this->settings->get('huseyinfiliz-guest.max_posts', 3);

        // Check if this is a guest user
        if (!preg_match('/^' . preg_quote($usernamePrefix, '/') . '\d{4}$/', $actor->username)) {
            return;
        }

        // Count existing posts
        $postCount = Post::where('user_id', $actor->id)->count();

        // Check limit
        if ($postCount >= $maxPosts) {
            throw new ValidationException([
                'message' => app('translator')->trans('huseyinfiliz-guest.forum.max_posts_reached', [
                    'max' => $maxPosts
                ])
            ]);
        }
    }
}