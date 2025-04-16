<?php

namespace App\Livewire\Disputs;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Disput;
use App\Models\DisputMessage;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Services\DisputChatService;
use App\Notifications\DisputAdminNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DisputShow extends Component
{
    use WithFileUploads;

    public $disputId;
    public $disput;
    public $messages;
    public $newMessage;
    public $currency;
    public $contrproposal = [
        'refund_amount' => null,
        'application_type' => null,
        'refund_method' => null,
        'message' => null,
        'evidence' => [],
    ];
    public $selectedMessageId;

    protected $chatService;

    public function boot()
    {
        Log::debug('DisputShow component booted', [
            'disputId' => $this->disputId ?? 'not set',
            'auth_user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'route' => request()->route()->getName(),
        ]);

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

        $this->disput = Disput::withoutGlobalScopes()->with('returnRequest')->find($disputId);
        if (!$this->disput) {
            Log::error('Disput not found', ['disputId' => $disputId]);
            abort(404);
        }
        Log::debug('Disput loaded', ['disput' => $this->disput->toArray()]);

        if ($this->disput->user_id !== Auth::id() && $this->disput->seller_id !== Auth::id() && Auth::user()->role_id !== 1) {
            Log::debug('403 Unauthorized');
            abort(403, 'Unauthorized');
        }

        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $this->currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        $this->loadMessages();
    }

    public function loadMessages()
    {
        try {
            $messages = $this->chatService->getMessages($this->disputId, $this->determineUserType());
            $this->messages = $messages->map(function ($message) {
                $message = (array) $message;
                $msg = DisputMessage::where('id', $message['id'])->first();
                return array_merge($message, [
                    'refund_amount' => $msg->refund_amount,
                    'application_type' => $msg->application_type,
                    'refund_method' => $msg->refund_method,
                    'evidence_path' => $msg->evidence_path, // Аксесор обробляє декодування
                    'proposal_status' => $msg->proposal_status,
                ]);
            })->toArray();
            Log::debug('Messages loaded', ['messages' => $this->messages]);
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

    public function acceptProposal($messageId)
    {
        $message = DisputMessage::where('id', $messageId)->where('disput_id', $this->disputId)->firstOrFail();
        if ($message->proposal_status !== 'open') {
            $this->addError('form', 'This proposal is no longer open.');
            return;
        }

        DisputMessage::create([
            'disput_id' => $this->disputId,
            'sender_id' => Auth::id(),
            'message' => 'Proposal accepted',
            'proposal_status' => 'accepted',
        ]);

        $message->update(['proposal_status' => 'accepted']);

        $returnRequest = ReturnRequest::find($this->disput->return_request_id);
        $returnRequest->update([
            'refund_amount' => $message->refund_amount,
            'application_type' => $message->application_type,
            'refund_method' => $message->refund_method,
        ]);

        $this->disput->update(['status' => 'accepted']);

        $this->loadMessages();
        session()->flash('message', 'Proposal accepted successfully!');
    }

    public function submitContrproposal()
    {
        if (!$this->selectedMessageId) {
            $this->addError('form', 'No message selected for counterproposal.');
            return;
        }

        $message = DisputMessage::where('id', $this->selectedMessageId)->where('disput_id', $this->disputId)->firstOrFail();
        if ($message->proposal_status !== 'open') {
            $this->addError('form', 'This proposal is no longer open.');
            return;
        }

        $this->validate([
            'contrproposal.refund_amount' => 'required|numeric|min:0|max:' . ($this->disput->returnRequest->orderItem->price * $this->disput->returnRequest->orderItem->quantity),
            'contrproposal.application_type' => 'required|in:' . implode(',', array_keys(config('application_types'))),
            'contrproposal.refund_method' => 'required|in:' . implode(',', array_keys(config('refund_methods'))),
            'contrproposal.message' => 'required|string|max:1000',
            'contrproposal.evidence.*' => 'nullable|file|mimes:jpeg,png,gif,mp4|max:10240',
        ]);

        $evidencePaths = [];
        if (!empty($this->contrproposal['evidence'])) {
            foreach ($this->contrproposal['evidence'] as $file) {
                if ($file->isValid()) {
                    $path = $file->store('return_evidences', 'public');
                    $evidencePaths[] = $path;
                }
            }
        }

        DisputMessage::create([
            'disput_id' => $this->disputId,
            'sender_id' => Auth::id(),
            'message' => $this->contrproposal['message'],
            'refund_amount' => $this->contrproposal['refund_amount'],
            'application_type' => $this->contrproposal['application_type'],
            'refund_method' => $this->contrproposal['refund_method'],
            'evidence_path' => json_encode($evidencePaths), // Кодування в JSON
            'proposal_status' => 'open',
        ]);

        $message->update(['proposal_status' => 'counter']);

        $this->contrproposal = [
            'refund_amount' => null,
            'application_type' => null,
            'refund_method' => null,
            'message' => null,
            'evidence' => [],
        ];
        $this->selectedMessageId = null;

        $this->loadMessages();
        session()->flash('message', 'Counterproposal submitted successfully!');
    }

    public function openContrproposalModal($messageId)
    {
        $this->selectedMessageId = $messageId;
        $this->dispatch('openContrproposalModal');
    }

    public function callAdmin($messageId)
    {
        $message = DisputMessage::where('id', $messageId)->where('disput_id', $this->disputId)->firstOrFail();
        if ($message->proposal_status !== 'open') {
            $this->addError('form', 'This proposal is no longer open.');
            return;
        }

        $this->disput->update([
            'status' => 'pending_admin',
            'admin_requested_at' => now(),
            'admin_requester_id' => Auth::id(),
        ]);

        DisputMessage::create([
            'disput_id' => $this->disputId,
            'sender_id' => Auth::id(),
            'message' => 'Admin intervention requested',
            'proposal_status' => 'admin_call',
        ]);

        $message->update(['proposal_status' => 'admin_call']);

        $admins = User::where('role_id', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new DisputAdminNotification($this->disput));
        }

        $this->loadMessages();
        session()->flash('message', 'Admin intervention requested successfully!');
    }

    protected function determineUserType()
    {
        if (Auth::user()->role_id === 1) {
            return 'admin';
        }
        return $this->disput->seller_id === Auth::id() ? 'seller' : 'user';
    }

    public function render()
    {
        return view('livewire.elegant.disputs.disput-show');
    }
}
