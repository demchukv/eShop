<?php

namespace App\Livewire\Disputs;

use Livewire\Component;
use App\Models\Disput;
use App\Services\DisputChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DisputShow extends Component
{
    public $disputId;
    public $disput;
    public $messages;
    public $newMessage;

    protected $chatService;

    public function boot()
    {
        Log::debug('DisputShow component booted', [
            'disputId' => $this->disputId ?? 'not set',
            'auth_user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'route' => request()->route()->getName(),
        ]);

        // Ініціалізуємо chatService, якщо воно null
        if (!$this->chatService) {
            $this->chatService = app(DisputChatService::class);
            Log::debug('DisputShow: Initialized chatService in boot');
        }
    }

    public function mount($disputId, DisputChatService $chatService)
    {
        Log::debug('DisputShow mount started', [
            'disputId' => $disputId,
            'auth_user_id' => Auth::id(),
            'session_id' => session()->getId(),
        ]);

        $this->chatService = $chatService;
        $this->disputId = $disputId;

        $this->disput = Disput::withoutGlobalScopes()->find($disputId);
        if (!$this->disput) {
            Log::error('Disput not found', ['disputId' => $disputId]);
            abort(404);
        }
        Log::debug('Disput loaded', ['disput' => $this->disput->toArray()]);

        $seller_id = \App\Models\Seller::where('user_id', Auth::id())->value('id');
        Log::debug('sellerId = ' . $seller_id);
        if ($this->disput->user_id !== Auth::id() && $this->disput->seller_id !== $seller_id && !Auth::user()->hasRole('super_admin')) {
            Log::debug('403 Unauthorized');
            abort(403, 'Unauthorized');
        }

        $this->loadMessages();
    }

    public function loadMessages()
    {
        try {
            $this->messages = $this->chatService->getMessages($this->disputId, $this->determineUserType())->toArray();
        } catch (\Exception $e) {
            Log::error('DisputShow: Error loading messages', [
                'disputId' => $this->disputId,
                'error' => $e->getMessage(),
            ]);
            $this->messages = [];
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        try {
            $this->chatService->sendMessage($this->disputId, $this->newMessage, $this->determineUserType());
            $this->newMessage = '';
            $this->loadMessages();
        } catch (\Exception $e) {
            Log::error('DisputShow: Error sending message', [
                'disputId' => $this->disputId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function determineUserType()
    {
        if (Auth::user()->hasRole('super_admin')) {
            return 'admin';
        }
        $seller_id = \App\Models\Seller::where('user_id', Auth::id())->value('id');
        return $this->disput->seller_id === $seller_id ? 'seller' : 'user';
    }

    public function render()
    {
        return view('livewire.elegant.disputs.disput-show');
    }
}
