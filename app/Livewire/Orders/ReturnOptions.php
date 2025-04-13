<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItems;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReturnOptions extends Component
{
    use WithFileUploads;

    public $orderItemId;
    public $user;
    public $orderItem;
    public $deliveryStatus;
    public $reason;
    public $applicationType;
    public $refundAmount;
    public $refundMethod;
    public $description;
    public $tempUploads = []; // Основний масив для файлів із унікальними ключами
    public $newUploads; // Тимчасова властивість для нових файлів

    public function mount($orderItemId)
    {
        \Log::info('Mount called for orderItemId: ' . $orderItemId, [
            'user' => Auth::check() ? Auth::user()->toArray() : 'Guest',
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);
        $this->orderItemId = $orderItemId;
        $this->user = Auth::user();

        $this->orderItem = OrderItems::where('id', $this->orderItemId)
            ->with(['productVariant.product', 'order'])
            ->firstOrFail();

        if ($this->orderItem->order->is_custom_courier != 1) {
            abort(404, 'Order item not found or custom courier not applicable');
        }

        $this->refundAmount = round($this->orderItem->price * $this->orderItem->quantity, 2);
    }

    public function updatedDeliveryStatus($value)
    {
        \Log::info('updatedDeliveryStatus called', ['value' => $value]);
        if ($value !== 'received') {
            $this->reason = null;
            $this->applicationType = null;
            $this->resetDependentFields();
        }
    }

    public function updatedReason($value)
    {
        \Log::info('updatedReason called', ['value' => $value]);
        if (!$value) {
            $this->applicationType = null;
            $this->resetDependentFields();
        }
    }

    public function updatedApplicationType($value)
    {
        \Log::info('updatedApplicationType called', ['value' => $value]);
        if (!$value) {
            $this->resetDependentFields();
        }
    }

    public function updatedNewUploads($value)
    {
        \Log::info('updatedNewUploads called', [
            'value' => is_array($value) ? array_map(fn($file) => $file->getFilename(), $value) : [],
            'current_tempUploads' => array_keys($this->tempUploads),
        ]);

        if (!empty($value)) {
            // Валідуємо нові файли
            $this->validate([
                'newUploads.*' => 'file|mimes:jpeg,png,gif,mp4|max:10240', // 10MB
            ]);

            // Додаємо нові файли до tempUploads з унікальними ключами
            foreach ($value as $file) {
                $key = Str::random(10);
                $this->tempUploads[$key] = $file;
                \Log::info('Added file with key', ['key' => $key, 'file' => $file->getFilename()]);
            }

            // Очищаємо newUploads після додавання
            $this->newUploads = null;
        }

        \Log::info('tempUploads after update', ['tempUploads' => array_keys($this->tempUploads)]);
    }

    private function resetDependentFields()
    {
        $this->refundMethod = null;
        $this->description = null;
        $this->tempUploads = [];
        $this->newUploads = null;
    }

    public function removeFile($key)
    {
        \Log::info('Removing file with key', ['key' => $key]);
        if (isset($this->tempUploads[$key])) {
            $file = $this->tempUploads[$key];
            if ($file->isValid()) {
                Storage::disk('local')->delete($file->getFilename());
            }
            unset($this->tempUploads[$key]);
        }
        \Log::info('tempUploads after removal', ['tempUploads' => array_keys($this->tempUploads)]);
    }

    public function saveReturnOption()
    {
        \Log::info('saveReturnOption called', [
            'deliveryStatus' => $this->deliveryStatus,
            'reason' => $this->reason,
            'applicationType' => $this->applicationType,
            'tempUploads' => count($this->tempUploads),
        ]);

        $rules = [
            'deliveryStatus' => 'required|in:received,not_received',
        ];

        if ($this->deliveryStatus === 'received') {
            $rules = array_merge($rules, [
                'reason' => 'required|in:no_longer_needed,mismatch_description,defective,damaged,missing_items,expire_date,wrong_item',
                'applicationType' => 'required|in:return_and_refund,refund_only',
            ]);

            if ($this->applicationType) {
                $rules = array_merge($rules, [
                    'refundAmount' => 'required|numeric|min:0|max:' . $this->orderItem->price * $this->orderItem->quantity,
                    'refundMethod' => 'required|in:wallet,original_payment',
                    'description' => 'required|string|max:500',
                    'tempUploads' => 'nullable|array',
                    'tempUploads.*' => 'file|mimes:jpeg,png,gif,mp4|max:10240', // 10MB
                ]);
            }
        }

        $this->validate($rules);

        $validationResult = validateOrderStatus($this->orderItemId, 'returned', 'order_items', '', true);

        if ($validationResult['error'] && !isset($validationResult['return_request_flag'])) {
            $this->addError('form', $validationResult['message']);
            return;
        }

        $updateResult = update_order_item($this->orderItemId, 'return_request_pending', 1);

        if ($updateResult['error']) {
            $this->addError('form', $updateResult['message']);
            return;
        }

        $returnRequest = ReturnRequest::where('order_item_id', $this->orderItemId)
            ->where('user_id', $this->user->id)
            ->latest()
            ->first();

        if (!$returnRequest) {
            $this->addError('form', 'Return request not found after creation.');
            return;
        }

        $evidencePath = $this->handleUpload();

        $returnRequest->update([
            'delivery_status' => $this->deliveryStatus,
            'reason' => $this->reason,
            'application_type' => $this->applicationType,
            'refund_amount' => $this->refundAmount,
            'refund_method' => $this->refundMethod,
            'description' => $this->description,
            'evidence_path' => $evidencePath,
        ]);

        session()->flash('message', 'Return request submitted successfully!');
        return redirect()->route('orders.details', $this->orderItem->order_id);
    }

    private function handleUpload()
    {
        \Log::info('handleUpload called', ['tempUploads' => count($this->tempUploads)]);
        if (empty($this->tempUploads)) {
            return null;
        }

        $paths = [];
        foreach ($this->tempUploads as $file) {
            if ($file->isValid()) {
                $path = $file->store('return_evidences', 'public');
                $paths[] = $path;
            }
        }

        \Log::info('Uploaded files', ['paths' => $paths]);
        return json_encode($paths);
    }

    public function render()
    {
        return view('livewire.elegant.orders.return-options', [
            'user_info' => $this->user,
            'order_item' => $this->orderItem,
        ]);
    }
}
