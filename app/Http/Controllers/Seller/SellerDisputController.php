<?php

namespace App\Http\Controllers\Seller;

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

class SellerDisputController extends Controller
{
    protected $chatService;

    public function __construct(DisputChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function show($id)
    {
        $disput = Disput::where('id', $id)
            ->where('seller_id', Auth::id())
            ->with(['returnRequest', 'returnRequest.orderItem', 'returnRequest.user'])
            ->firstOrFail();

        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        // Завантаження списку перевізників
        $afterShipController = app(\App\Http\Controllers\AfterShipApiController::class);
        $userCountry = Auth::user()->country ?? 'HKG';
        $response = $afterShipController->getCouriersList(new \Illuminate\Http\Request(['country' => $userCountry]));
        $data = json_decode($response->getContent(), true);
        Log::debug('AfterShip couriers response', ['data' => $data]);
        $couriers = $data['data']['couriers'] ?? $data['couriers'] ?? [];
        if (empty($couriers)) {
            $couriers = [
                ['slug' => 'gols', 'name' => 'GO Logistics & Storage'],
                ['slug' => 'india-post', 'name' => 'India Post Domestic'],
            ];
        }

        return view('seller.pages.disput.show', compact('disput', 'currency', 'couriers'));
    }

    public function messages($id)
    {
        $messages = $this->chatService->getMessages($id, 'seller');
        $application_types = config('application_types');
        $refund_methods = config('refund_methods');
        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';
        $disput = Disput::findOrFail($id);
        $can_respond = $disput->messages()->where('proposal_status', 'open')
            ->where('sender_id', $disput->user_id)
            ->exists() && $disput->seller_id === Auth::id();

        return response()->json([
            'messages' => $messages,
            'application_types' => $application_types,
            'refund_methods' => $refund_methods,
            'currency' => $currency,
            'disput_status' => $disput->status,
            'can_respond' => $can_respond,
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $response = $this->chatService->sendMessage($id, $request->message, 'seller');
        return response()->json($response);
    }

    public function acceptProposal(Request $request, $id, $messageId)
    {
        $response = $this->chatService->acceptProposal($id, $messageId, 'seller');
        return response()->json($response);
    }

    public function submitContrproposal(Request $request, $id, $messageId)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . ($this->getMaxRefund($id)),
            'application_type' => 'required|in:' . implode(',', array_keys(config('application_types'))),
            'refund_method' => 'required|in:' . implode(',', array_keys(config('refund_methods'))),
            'message' => 'required|string|max:1000',
            'evidence.*' => 'nullable|file|mimes:jpeg,png,gif,mp4|max:10240',
        ]);

        $data = $request->only(['refund_amount', 'application_type', 'refund_method', 'message']);
        $files = $request->file('evidence', []);

        $response = $this->chatService->submitContrproposal($id, $messageId, $data, $files, 'seller');
        return response()->json($response);
    }

    public function callAdmin(Request $request, $id, $messageId)
    {
        $response = $this->chatService->callAdmin($id, $messageId, 'seller');
        return response()->json($response);
    }

    public function submitTracking(Request $request, $id)
    {
        $disput = Disput::where('id', $id)
            ->where('seller_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'tracking_number' => 'required|string|max:255',
            'courier_service' => 'required|string|max:255',
        ]);

        try {
            $this->chatService->submitTracking($id, $request->tracking_number, $request->courier_service, 'seller');
            return redirect()->route('seller.disput.show', $id)
                ->with('success', 'Tracking information submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('seller.disput.show', $id)
                ->withErrors(['form' => 'Failed to submit tracking information: ' . $e->getMessage()]);
        }
    }

    public function updateReturnStatus(Request $request, $id)
    {
        $disput = Disput::where('id', $id)
            ->where('seller_id', Auth::id())
            ->firstOrFail();
        $returnRequest = ReturnRequest::findOrFail($disput->return_request_id);

        $request->validate([
            'status' => 'required|in:2,3,4',
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

            return redirect()->route('seller.disput.show', $id)
                ->with('success', 'Return status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update return status', [
                'disput_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('seller.disput.show', $id)
                ->withErrors(['form' => 'Failed to update return status: ' . $e->getMessage()]);
        }
    }



    protected function getMaxRefund($disputId)
    {
        $disput = Disput::where('id', $disputId)->with('returnRequest.orderItem')->firstOrFail();
        return $disput->returnRequest->orderItem->price * $disput->returnRequest->orderItem->quantity;
    }
}
