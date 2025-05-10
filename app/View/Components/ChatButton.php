<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ChatButton extends Component
{
    public $userId;
    public $buttonText;

    public function __construct($userId, $buttonText = 'Chat with seller')
    {
        $this->userId = $userId;
        $this->buttonText = $buttonText;
    }

    public function render()
    {
        return view('components.chat-button');
    }
}
