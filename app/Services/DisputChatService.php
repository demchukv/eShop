<?php

namespace App\Services;

use App\Models\Disput;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;

class DisputChatService
{
    public function getMessages($disputId, $userType = 'user')
    {
        \Log::debug('DisputChatService getMessages', [
            'disputId' => $disputId,
            'userType' => $userType,
            'auth_user_id' => Auth::id(),
        ]);

        $disput = Disput::where('id', $disputId)->firstOrFail();

        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller') {
            $seller_id = Seller::where('user_id', Auth::id())->value('id');
            if ($disput->seller_id !== $seller_id) {
                abort(403, 'Unauthorized');
            }
        } elseif ($userType === 'admin' && !Auth::user()->role_id == 1) {
            abort(403, 'Unauthorized');
        }

        $messages = $disput->messages()->with('sender')->orderBy('created_at', 'asc')->get()->map(function ($message) use ($disput, $userType) {
            // dd($message);
            $senderName = $message->sender ? $message->sender->username : 'Unknown';
            $senderType = $message->sender && $message->sender->id === Auth::id() ? $userType : ($message->sender && $message->sender->id === $disput->seller->user_id ? 'seller' : 'user');

            return [
                'id' => $message->id,
                'disput_id' => $message->disput_id,
                'sender_id' => $message->sender_id,
                'sender_type' => $senderType,
                'sender_name' => $senderName,
                'message' => $message->message,
                'created_at' => $message->created_at->toIso8601String(),
            ];
        });

        return $messages;
    }

    public function sendMessage($disputId, $message, $userType = 'user')
    {
        $disput = Disput::where('id', $disputId)->firstOrFail();

        // Check access
        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            \Log::info('Disput access check', [
                'userType' => $userType,
                'user_id' => Auth::id(),
                'disput_user_id' => $disput->user_id,
                'disput_seller_id' => $disput->seller_id,
                'seller_id_from_data' => Seller::where('user_id', Auth::id())->value('id'),
            ]);
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller') {
            $seller_id = Seller::where('user_id', Auth::id())->value('id');
            if ($disput->seller_id !== $seller_id) {
                abort(403, 'Unauthorized');
            }
        } elseif ($userType === 'admin' && !Auth::user()->role_id == 1) {
            abort(403, 'Unauthorized');
        }

        $disput->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $message,
        ]);

        return ['message' => 'Message sent successfully'];
    }
}
