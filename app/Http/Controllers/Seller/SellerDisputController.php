<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Disput;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDisputController extends Controller
{
    public function show($id)
    {
        // Get the seller ID
        $seller_id = Seller::where('user_id', Auth::id())->value('id');

        // Fetch the dispute, ensuring it belongs to the seller
        $disput = Disput::where('id', $id)
            ->where('seller_id', $seller_id)
            ->with(['returnRequest', 'returnRequest.orderItem', 'returnRequest.user', 'messages', 'messages.sender'])
            ->firstOrFail();

        // Define the currency
        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        return view('seller.pages.disput.show', compact('disput', 'seller_id', 'currency'));
    }

    public function messages($id)
    {
        $seller_id = Seller::where('user_id', Auth::id())->value('id');
        $disput = Disput::where('id', $id)
            ->where('seller_id', $seller_id)
            ->firstOrFail();

        $messages = $disput->messages()->with('sender')->orderBy('created_at', 'asc')->get()->map(function ($message) {
            // Determine the sender's name
            $senderName = $message->sender ? $message->sender->username : 'Unknown';
            $senderType = $message->sender && $message->sender->id === Auth::id() ? 'seller' : 'user';

            return [
                'sender_type' => $senderType,
                'sender_name' => $senderName,
                'message' => $message->message,
                'created_at' => $message->created_at->toIso8601String(),
            ];
        });

        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(Request $request, $id)
    {
        $seller_id = Seller::where('user_id', Auth::id())->value('id');
        $disput = Disput::where('id', $id)
            ->where('seller_id', $seller_id)
            ->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $disput->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Message sent successfully']);
    }
}
