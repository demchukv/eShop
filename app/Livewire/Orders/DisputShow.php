<?php

namespace App\Livewire\Orders\Disputs;

use Livewire\Component;
use App\Models\Disput;
use App\Models\DisputMessage;
use Illuminate\Support\Facades\Auth;

class DisputShow extends Component
{
    public $disputId;
    public $disput;
    public $messages = [];
    public $newMessage;

    public function mount($disputId)
    {
        $this->disputId = $disputId;
        $this->disput = Disput::with(['messages.sender', 'returnRequest'])->findOrFail($disputId);

        // Перевірка доступу
        if ($this->disput->user_id !== Auth::id() && $this->disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = $this->disput->messages()->with('sender')->latest()->get()->reverse();
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        DisputMessage::create([
            'disput_id' => $this->disputId,
            'sender_id' => Auth::id(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.elegant.orders.disput-show');
    }
}
