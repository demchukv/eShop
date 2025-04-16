<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disput;
use App\Models\DisputMessage;
use App\Models\ReturnRequest;
use App\Services\DisputChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDisputController extends Controller
{
    protected $chatService;

    public function __construct(DisputChatService $chatService)
    {
        \Log::debug('start admin disput controller');
        $this->chatService = $chatService;
    }

    public function show($id)
    {
        \Log::debug('USER: ' . json_encode(Auth::user()));
        if (Auth::user()->role_id !== 1) {
            \Log::debug("User haven't access");
            abort(403, 'Unauthorized');
        }

        $disput = Disput::where('id', $id)
            ->with(['returnRequest', 'returnRequest.orderItem', 'returnRequest.user'])
            ->firstOrFail();

        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        return view('admin.pages.disput.show', compact('disput', 'currency'));
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

        $returnRequest = ReturnRequest::find($disput->return_request_id);
        $returnRequest->update([
            'refund_amount' => $request->refund_amount,
            'application_type' => $request->application_type,
            'refund_method' => $request->refund_method,
            'status' => 1, // Approved
        ]);

        DisputMessage::create([
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

    protected function getMaxRefund($disputId)
    {
        $disput = Disput::where('id', $disputId)->with('returnRequest.orderItem')->firstOrFail();
        return $disput->returnRequest->orderItem->price * $disput->returnRequest->orderItem->quantity;
    }
}
