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
        $seller_id = Seller::where('user_id', Auth::id())->value('id');

        $disput = Disput::where('id', $id)
            ->where('seller_id', $seller_id)
            ->with(['returnRequest', 'returnRequest.orderItem', 'returnRequest.user'])
            ->firstOrFail();


        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        return view('seller.pages.disput.show', compact('disput', 'seller_id', 'currency'));
    }

    public function messages($id)
    {
        $messages = $this->chatService->getMessages($id, 'seller');
        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $response = $this->chatService->sendMessage($id, $request->message, 'seller');
        return response()->json($response);
    }
}
