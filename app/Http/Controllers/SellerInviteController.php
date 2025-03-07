<?php

namespace App\Http\Controllers;

use App\Models\SellerInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SellerInviteController extends Controller
{
    /**
     * Створення нового реферального посилання.
     */
    public function store(Request $request)
    {
        // Перевірка авторизації (якщо потрібно)
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Увійдіть, щоб створити посилання');
        }

        // Генерація унікального коду (25 символів)
        $link = Str::random(25);

        // Створення запису в таблиці seller_invites
        $sellerInvite = SellerInvite::create([
            'link' => $link,
            'user_id' => Auth::id(), // ID поточного користувача
        ]);

        return redirect()->back()->with('success', 'Реферальне посилання створено: ' . $link);
    }

    /**
     * Відображення списку реферальних посилань користувача.
     */
    public function index()
    {
        $invites = SellerInvite::where('user_id', Auth::id())->get();

        return view('seller_invites.index', compact('invites'));
    }

    /**
     * Видалення реферального посилання.
     */
    public function destroy(SellerInvite $sellerInvite)
    {
        // Перевірка, чи належить посилання користувачу
        if ($sellerInvite->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Ви не можете видалити це посилання');
        }

        $sellerInvite->delete();

        return redirect()->back()->with('success', 'Посилання видалено');
    }
}
