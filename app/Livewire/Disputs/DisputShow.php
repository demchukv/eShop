<?php

namespace App\Livewire\Disputs;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Disput;
use App\Models\DisputMessage;
use App\Models\ReturnRequest;
use App\Models\OrderTracking;
use App\Models\User;
use App\Services\DisputChatService;
use App\Notifications\DisputAdminNotification;
use App\Http\Controllers\AfterShipApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
    public $tracking = [
        'tracking_number' => null,
        'courier_service' => null,
    ];
    public $couriers = [];

    protected $chatService;

    public function boot()
    {

        if (!$this->chatService) {
            $this->chatService = app(DisputChatService::class);
        }
    }

    public function mount($disputId, DisputChatService $chatService)
    {

        $this->chatService = $chatService;
        $this->disputId = $disputId;

        $this->disput = Disput::withoutGlobalScopes()->with('returnRequest')->find($disputId);
        if (!$this->disput) {
            Log::error('Disput not found', ['disputId' => $disputId]);
            abort(404);
        }

        if ($this->disput->user_id !== Auth::id() && $this->disput->seller_id !== Auth::id() && Auth::user()->role_id !== 1) {
            Log::debug('403 Unauthorized');
            abort(403, 'Unauthorized');
        }

        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $this->currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        $this->loadCouriers();
        $this->loadMessages();
    }

    protected function loadCouriers()
    {
        try {
            $afterShipController = app(AfterShipApiController::class);
            $response = $afterShipController->getCouriersList();
            $data = json_decode($response->getContent(), true);
            if (isset($data['couriers'])) {
                // Фільтрація за країною (наприклад, країна користувача)
                // $userCountry = Auth::user()->country ?? 'HKG'; // Замініть на реальне поле
                // $this->couriers = array_filter($data['couriers'], function ($courier) use ($userCountry) {
                //     return in_array($userCountry, $courier['service_from_country_regions']);
                // });
                // Log::debug('Couriers loaded', ['couriers_count' => count($this->couriers), 'filtered_by' => $userCountry]);
                $this->couriers = $data['couriers'];
            } else {
                Log::warning('No couriers found in response', ['response' => $data]);
                $this->couriers = [];
            }
        } catch (\Exception $e) {
            $this->couriers = [];
            $this->addError('form', 'Failed to load couriers. Please try again.');
        }
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
                    'evidence_path' => $msg->evidence_path,
                    'proposal_status' => $msg->proposal_status,
                ]);
            })->toArray();
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
        // $this->chatService->acceptProposal($this->disputId, $messageId, 'user');

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
            'refund_amount' => $message->refund_amount,
            'application_type' => $message->application_type,
            'refund_method' => $message->refund_method,
        ]);

        $message->update(['proposal_status' => 'accepted']);

        $returnRequest = ReturnRequest::find($this->disput->return_request_id);
        $returnRequest->update([
            'refund_amount' => $message->refund_amount,
            'application_type' => $message->application_type,
            'refund_method' => $message->refund_method,
            'status' => 2, // approved
        ]);

        // $this->disput->update(['status' => 'accepted']);

        $this->loadMessages();
        session()->flash('message', 'Proposal accepted successfully!');
    }

    public function resolveDisput($refundAmount, $applicationType, $refundMethod)
    {
        if (Auth::user()->role_id !== 1) {
            Log::debug('403 Unauthorized: Only admins can resolve disputes');
            abort(403, 'Unauthorized');
        }

        $this->validate([
            'refundAmount' => 'required|numeric|min:0|max:' . ($this->disput->returnRequest->orderItem->price * $this->disput->returnRequest->orderItem->quantity),
            'applicationType' => 'required|in:' . implode(',', array_keys(config('application_types'))),
            'refundMethod' => 'required|in:' . implode(',', array_keys(config('refund_methods'))),
        ], [
            'refundAmount' => $refundAmount,
            'applicationType' => $applicationType,
            'refundMethod' => $refundMethod,
        ]);

        try {
            DisputMessage::create([
                'disput_id' => $this->disputId,
                'sender_id' => Auth::id(),
                'sender_type' => 'admin',
                'message' => 'Admin resolved the dispute',
                'proposal_status' => 'accepted',
                'refund_amount' => $refundAmount,
                'application_type' => $applicationType,
                'refund_method' => $refundMethod,
            ]);

            $returnRequest = ReturnRequest::find($this->disput->return_request_id);
            $returnRequest->update([
                'refund_amount' => $refundAmount,
                'application_type' => $applicationType,
                'refund_method' => $refundMethod,
                'status' => 2, // approved
            ]);

            $this->disput->update(['status' => 'accepted']);

            $this->loadMessages();
            session()->flash('message', 'Dispute resolved successfully!');
        } catch (\Exception $e) {
            Log::error('DisputShow: Error resolving dispute', [
                'disputId' => $this->disputId,
                'error' => $e->getMessage(),
            ]);
            $this->addError('form', 'Failed to resolve dispute. Please try again.');
        }
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
            'evidence_path' => json_encode($evidencePaths),
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
        $this->dispatch('closeContrproposalModal');
        session()->flash('message', 'Counterproposal submitted successfully!');
    }

    public function openContrproposalModal($messageId)
    {
        $this->selectedMessageId = $messageId;
        $message = DisputMessage::where('id', $messageId)->where('disput_id', $this->disputId)->firstOrFail();

        $this->contrproposal = [
            'refund_amount' => $message->refund_amount ?? null,
            'application_type' => $message->application_type ?? null,
            'refund_method' => $message->refund_method ?? null,
            'message' => null,
            'evidence' => [],
        ];

        $this->dispatch('openContrproposalModal');
    }

    public function callAdmin($messageId)
    {

        $this->chatService->callAdmin($this->disputId, $messageId, 'user');
        $this->loadMessages();
        session()->flash('message', 'Admin intervention requested successfully!');

        // $message = DisputMessage::where('id', $messageId)->where('disput_id', $this->disputId)->firstOrFail();
        // if ($message->proposal_status !== 'open') {
        //     $this->addError('form', 'This proposal is no longer open.');
        //     return;
        // }

        // try {
        //     $this->disput->update([
        //         'status' => 'pending_admin',
        //         'admin_requested_at' => now(),
        //         'admin_requester_id' => Auth::id(),
        //     ]);

        //     DisputMessage::create([
        //         'disput_id' => $this->disputId,
        //         'sender_id' => Auth::id(),
        //         'message' => 'Admin intervention requested',
        //         'proposal_status' => 'admin_call',
        //     ]);

        //     $message->update(['proposal_status' => 'admin_call']);

        //     $admins = User::where('role_id', 1)->get();

        //     foreach ($admins as $admin) {
        //         try {
        //             $admin->notify(new DisputAdminNotification($this->disput));
        //         } catch (\Exception $e) {
        //         }
        //     }

        //     $this->loadMessages();
        //     session()->flash('message', 'Admin intervention requested successfully!');
        // } catch (\Exception $e) {
        //     $this->addError('form', 'Failed to request admin intervention. Please try again.');
        // }
    }

    public function submitTracking()
    {
        if ($this->disput->user_id !== Auth::id()) {
            Log::debug('403 Unauthorized: Only the client can submit tracking');
            $this->addError('form', 'Only the client can submit tracking information.');
            return;
        }

        $this->validate([
            'tracking.tracking_number' => 'required|string|max:255',
            'tracking.courier_service' => 'required|string|max:255',
        ]);

        try {
            $afterShipSuccess = false;
            $returnRequest = ReturnRequest::find($this->disput->return_request_id);
            if (!$returnRequest) {
                Log::error('DisputShow: ReturnRequest not found', [
                    'disputId' => $this->disputId,
                    'return_request_id' => $this->disput->return_request_id,
                ]);
                $this->addError('form', 'Return request not found.');
                return;
            }

            // Використовуємо транзакцію для консистентності
            DB::transaction(function () use ($returnRequest) {
                // Створюємо OrderTracking незалежно від AfterShip
                $orderTracking = OrderTracking::create([
                    'order_id' => $returnRequest->order_id,
                    'order_item_id' => $returnRequest->order_item_id,
                    'tracking_number' => $this->tracking['tracking_number'],
                    'courier_agency' => $this->tracking['courier_service'],
                    'carrier_id' => $this->tracking['courier_service'],
                    'tracking_id' => $this->tracking['tracking_number'],
                    'parcel_id' => null, // Позначаємо як повернення
                    'status' => 'pending',
                    'date' => now(),
                    'aftership_tracking_id' => null,
                    'aftership_data' => null,
                    'url' => '',
                ]);

                // Спробуємо зареєструвати трекінг у AfterShip
                $afterShipController = app(AfterShipApiController::class);
                $response = $afterShipController->createTracking(new \Illuminate\Http\Request([
                    'order_id' => $returnRequest->order_id,
                    'parcel_id' => null,
                    'courier_agency' => $this->tracking['courier_service'],
                    'tracking_id' => $this->tracking['tracking_number'],
                ]));

                $responseData = json_decode($response->getContent(), true);
                $afterShipSuccess = $response->getStatusCode() === 201 && isset($responseData['tracking']['id']);

                if ($afterShipSuccess) {
                    // Оновлюємо OrderTracking при успіху
                    $orderTracking->update([
                        'aftership_tracking_id' => $responseData['tracking']['id'],
                        'aftership_data' => json_encode($responseData['tracking']),
                        'url' => $responseData['tracking']['tracking_url'] ?? null,
                    ]);
                } else {
                    // Логуємо помилку, але зберігаємо OrderTracking
                    Log::warning('DisputShow: Failed to create AfterShip tracking, saving OrderTracking anyway', [
                        'disputId' => $this->disputId,
                        'tracking_number' => $this->tracking['tracking_number'],
                        'courier_service' => $this->tracking['courier_service'],
                        'response' => $responseData,
                    ]);
                }

                // Оновлюємо ReturnRequest
                $returnRequest->update([
                    'order_tracking_id' => $orderTracking->id,
                    'status' => 3, // return_pickedup
                ]);

                // Додаємо повідомлення в чат
                $message = 'Tracking information submitted: ' . $this->tracking['tracking_number'] . ' (' . $this->tracking['courier_service'] . ')';
                if (!$afterShipSuccess) {
                    $message .= ' (Failed to register with AfterShip, please contact support)';
                }
                DisputMessage::create([
                    'disput_id' => $this->disputId,
                    'sender_id' => Auth::id(),
                    'message' => $message,
                    'proposal_status' => 'tracking_submitted',
                ]);
                $afterShipSuccess = $response->getStatusCode() === 201 && isset($responseData['tracking']['id']);
            });

            $this->tracking = [
                'tracking_number' => null,
                'courier_service' => null,
            ];

            $this->loadMessages();
            session()->flash('message', 'Tracking information submitted successfully' . ($afterShipSuccess ? '' : ', but AfterShip registration failed. Contact support for retry.'));
        } catch (\Exception $e) {
            Log::error('DisputShow: Error submitting tracking', [
                'disputId' => $this->disputId,
                'tracking_number' => $this->tracking['tracking_number'],
                'courier_service' => $this->tracking['courier_service'],
                'error' => $e->getMessage(),
            ]);
            $this->addError('form', 'Failed to submit tracking information: ' . $e->getMessage());
        }
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
        return view('livewire.elegant.disputs.disput-show', [
            'couriers' => $this->couriers,
        ]);
    }
}
