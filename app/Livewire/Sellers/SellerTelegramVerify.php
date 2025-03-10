<?php

namespace App\Livewire\Sellers;

use App\Models\SellerInvite;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SellerTelegramVerify extends Component
{
    public $link;
    public $invite;
    public $user_info;
    public $message = '';
    public $error_message = "";
    public $telegram_id = '';
    public $telegram_username = '';
    public $telegramVerified = false;

    protected $rules = [
        'telegram_id' => 'required|string',
        'telegram_username' => 'required|string',
    ];

    public function mount($link)
    {
        $this->link = $link;
        $this->invite = SellerInvite::where('link', $this->link)->first();

        if (!$this->invite || $this->invite->isExpired()) {
            $this->message = 'Invalid or expired invitation link.';
            return;
        }

        if ($this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'This invitation link has already been used or has expired.';
            return;
        }

        $this->user_info = USER::where('id', $this->invite->user_id)->first();
        if (!$this->user_info) {
            $this->message = 'Invalid or expired invitation link.';
            return;
        }
    }

    public function verifyTelegram($telegramData)
    {
        $user = $telegramData;
        if (!isset($user['username'])) {
            $user['username'] = $user['id'];
        }

        $this->telegram_id = (string) $user['id']; // Конвертуємо в рядок
        $this->telegram_username = $user['username'];
        $this->telegramVerified = true;

        $this->validate();

        // $exists_user = USER::where('telegram_id', $this->telegram_id)->first();
        // if ($exists_user) {
        //     $this->error_message = 'User with this Telegram ID already exists.';
        //     return;
        // }

        // Зберігаємо дані в сесію
        session()->put('seller_telegram_data', [
            'telegram_id' => $this->telegram_id,
            'telegram_username' => $this->telegram_username,
            'invite_link' => $this->link,
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
        ]);

        // Перенаправляємо на другу частину
        return redirect()->route('seller.register.complete', ['link' => $this->link]);
    }

    public function render()
    {
        if (!$this->telegramVerified) {
            $this->dispatch('reinit-telegram');
        }
        return view('livewire.elegant.sellers.seller-telegram-verify');
    }
}
