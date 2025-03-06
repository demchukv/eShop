<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\CustomChatifyMessenger;
use Chatify\ChatifyMessenger;

class ChatifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ChatifyMessenger::class, CustomChatifyMessenger::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
