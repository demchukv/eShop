<?php

namespace App\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class WeatherCommand extends Command
{

    protected string $name = 'weather';
    protected string $description = 'Information about weather';

    public function handle()
    {
        $keyboard = new Keyboard();

        $button = Keyboard::button([
            'text' => 'Send location',
            'request_location' => true,
        ]);

        $keyboard->setResizeKeyboard(true);
        $keyboard->setOneTimeKeyboard(true);

        $keyboard->row(array($button));

        $this->replyWithMessage([
            'text' => 'For getting weather - click button',
            'reply_markup' => $keyboard,
        ]);
    }
}
