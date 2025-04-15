<?php

namespace App\Livewire\Disputs;

use Livewire\Component;
use App\Models\Disput;
use App\Models\DisputMessage;
use Illuminate\Support\Facades\Auth;

class DisputShow extends Component
{
    public $disputId;
    public $disput;
    public $messages;
    public $newMessage;

    public function mount($disputId)
    {
        $this->disputId = $disputId;
        $this->disput = Disput::with(['messages.sender', 'returnRequest'])->findOrFail($disputId);

        if ($this->disput->user_id !== Auth::id() && $this->disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->loadMessages();
    }

    public function loadMessages()
    {
        $messages = $this->disput->messages()->with('sender')->latest()->get()->toArray();
        $this->messages = array_reverse($messages);

        \Log::info('Messages loaded', [
            'disput_id' => $this->disputId,
            'messages_type' => gettype($this->messages),
            'messages_count' => count($this->messages),
        ]);
    }

    public function sendMessage()
    {
        \Log::info('sendMessage called', [
            'disput_id' => $this->disputId,
            'messages_type' => gettype($this->messages),
        ]);

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

        \Log::info('Messages after load', [
            'disput_id' => $this->disputId,
            'messages_type' => gettype($this->messages),
            'messages_count' => count($this->messages),
        ]);
    }

    public function hydrate()
    {
        \Log::info('Component hydrated', [
            'disput_id' => $this->disputId,
            'disput_exists' => !is_null($this->disput),
        ]);
    }

    public function render()
    {
        \Log::info('Rendering component', [
            'disput_id' => $this->disputId,
            'disput_exists' => !is_null($this->disput),
        ]);

        return view('livewire.elegant.disputs.disput-show');
    }
}
