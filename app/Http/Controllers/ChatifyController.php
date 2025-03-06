<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatifyController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        $message = $request->message;
        // $user = $request->user();
        // $user->messages()->create([
        //     'message' => $message,
        //     'user_id' => $user->id,
        // ]);
        return response()->json(['success' => true, 'message' => 'Message sent successfully']);
    }
}
