<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disput;
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
        if (!Auth::user()->role_id == 1) {
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
        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $response = $this->chatService->sendMessage($id, $request->message, 'admin');
        return response()->json($response);
    }
}
