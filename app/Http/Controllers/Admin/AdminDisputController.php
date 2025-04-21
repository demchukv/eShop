<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommissionController;
use App\Models\Disput;
use App\Models\ReturnRequest;
use App\Models\Transaction;
use App\Services\DisputChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Refund;
use Stripe\Stripe as StripeConfig;

class AdminDisputController extends Controller
{
    protected $chatService;

    public function __construct(DisputChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function show($id)
    {
        if (Auth::user()->role_id !== 1) {
            Log::debug("User haven't access");
            abort(403, 'Unauthorized');
        }

        $disput = Disput::where('id', $id)
            ->with(['returnRequest', 'returnRequest.orderItem', 'returnRequest.user'])
            ->firstOrFail();

        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        // Завантаження списку перевізників
        $afterShipController = app(\App\Http\Controllers\AfterShipApiController::class);
        $userCountry = Auth::user()->country ?? 'HKG';
        $response = $afterShipController->getCouriersList(new \Illuminate\Http\Request(['country' => $userCountry]));
        $data = json_decode($response->getContent(), true);
        $couriers = $data['data']['couriers'] ?? $data['couriers'] ?? [];
        if (empty($couriers)) {
            $couriers = [
                ['slug' => 'gols', 'name' => 'GO Logistics & Storage'],
                ['slug' => 'india-post', 'name' => 'India Post Domestic'],
            ];
        }

        return view('admin.pages.disput.show', compact('disput', 'currency', 'couriers'));
    }

    public function messages($id)
    {
        $messages = $this->chatService->getMessages($id, 'admin');
        $application_types = config('application_types');
        $refund_methods = config('refund_methods');
        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';
        $disput = Disput::findOrFail($id);

        return response()->json([
            'messages' => $messages,
            'application_types' => $application_types,
            'refund_methods' => $refund_methods,
            'currency' => $currency,
            'disput_status' => $disput->status,
            'can_respond' => false,
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $response = $this->chatService->sendMessage($id, $request->message, 'admin');
        return response()->json($response);
    }

    public function resolve(Request $request, $id)
    {
        if (Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        $disput = Disput::where('id', $id)->firstOrFail();

        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . ($this->getMaxRefund($id)),
            'application_type' => 'required|in:' . implode(',', array_keys(config('application_types'))),
            'refund_method' => 'required|in:' . implode(',', array_keys(config('refund_methods'))),
            'comment' => 'nullable|string|max:1000',
        ]);

        $returnRequest = \App\Models\ReturnRequest::find($disput->return_request_id);
        $returnRequest->update([
            'refund_amount' => $request->refund_amount,
            'application_type' => $request->application_type,
            'refund_method' => $request->refund_method,
            'status' => 1, // Approved
        ]);

        \App\Models\DisputMessage::create([
            'disput_id' => $id,
            'sender_id' => Auth::id(),
            'message' => $request->comment ?? 'Disput resolved by admin.',
            'refund_amount' => $request->refund_amount,
            'application_type' => $request->application_type,
            'refund_method' => $request->refund_method,
            'proposal_status' => 'accepted',
        ]);

        $disput->update(['status' => 'closed']);

        return redirect()->route('admin.disput.show', $id)->with('success', 'Disput resolved successfully.');
    }

    public function submitTracking(Request $request, $id)
    {
        if (Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'tracking_number' => 'required|string|max:255',
            'courier_service' => 'required|string|max:255',
        ]);

        try {
            $this->chatService->submitTracking($id, $request->tracking_number, $request->courier_service, 'admin');
            return redirect()->route('admin.disput.show', $id)
                ->with('success', 'Tracking information submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.disput.show', $id)
                ->withErrors(['form' => 'Failed to submit tracking information: ' . $e->getMessage()]);
        }
    }

    public function updateReturnStatus(Request $request, $id)
    {
        if (Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        $disput = Disput::where('id', $id)->firstOrFail();
        $returnRequest = ReturnRequest::findOrFail($disput->return_request_id);

        $request->validate([
            'status' => 'required|in:0,1,2,3,4',
        ]);

        try {
            $oldStatus = $returnRequest->status;
            $returnRequest->update(['status' => $request->status]);

            // Додаємо повідомлення в чат про зміну статусу
            \App\Models\DisputMessage::create([
                'disput_id' => $id,
                'sender_id' => Auth::id(),
                'message' => sprintf(
                    'Return status changed from %s to %s',
                    config('return_requests.statuses')[$oldStatus]['label'] ?? 'Unknown',
                    config('return_requests.statuses')[$request->status]['label'] ?? 'Unknown'
                ),
                'proposal_status' => 'status_updated',
            ]);

            // Обробка повернення коштів для статусу 4 (returned)
            if ($request->status == 4) {
                $refundService = new \App\Services\RefundService();
                $refundService->processReturnRefund($returnRequest);
            }

            return redirect()->route('admin.disput.show', $id)
                ->with('success', 'Return status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update return status', [
                'disput_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('admin.disput.show', $id)
                ->withErrors(['form' => 'Failed to update return status: ' . $e->getMessage()]);
        }
    }


    protected function getMaxRefund($disputId)
    {
        $disput = Disput::where('id', $disputId)->with('returnRequest.orderItem')->firstOrFail();
        return $disput->returnRequest->orderItem->price * $disput->returnRequest->orderItem->quantity;
    }
}
