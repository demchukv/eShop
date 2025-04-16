<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Disput;
use App\Models\Seller;
use App\Services\DisputChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('seller.pages.disput.show', compact('disput', 'currency'));
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

    protected function getMaxRefund($disputId)
    {
        $disput = Disput::where('id', $disputId)->with('returnRequest.orderItem')->firstOrFail();
        return $disput->returnRequest->orderItem->price * $disput->returnRequest->orderItem->quantity;
    }
}
