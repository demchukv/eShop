<?php

namespace App\Livewire\Sellers;

use App\Models\SellerInvite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class SellerRegister extends Component
{



    public $link;
    public $invite;
    public $message = '';
    public $user_info;

    public $telegram_id = '';
    public $telegram_username = '';
    public $username = '';
    public $mobile = '';
    public $email = '';
    public $first_name = '';
    public $last_name = '';
    public $password = '';
    public $password_confirmation = '';
    public $agree = false;

    public $telegramVerified = false; // Додаємо для відстеження стану Telegram


    protected $rules = [
        'telegram_id' => 'required|string',
        'telegram_username' => 'required|string',
        'username' => 'required|string|max:255|unique:users,username',
        'mobile' => 'required|numeric|digits_between:10,15|unique:users,mobile',
        'email' => 'required|email|max:255|unique:users,email',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'password' => 'required|string|min:8|confirmed',
        'agree' => 'accepted',
    ];

    protected $messages = [
        'agree.accepted' => 'You must agree to the terms and policy.',
        'password.confirmed' => 'The password confirmation does not match.',
    ];

    public function mount($link)
    {

        $this->link = $link;
        // $this->system_settings = cache()->remember('system_settings', 3600, function () {
        //     return \App\Models\SystemSetting::pluck('value', 'key')->toArray();
        // });

        // Перевіряємо, чи існує запрошення з таким посиланням
        $this->invite = SellerInvite::where('link', $this->link)->first();

        if (!$this->invite || $this->invite->isExpired()) {
            $this->message = 'Invalid or expired invitation link.';
            return;
        }

        // Перевіряємо статус запрошення
        if ($this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'This invitation link has already been used or has expired.';
            return;
        }

        // Якщо користувач авторизований, можна додати логіку (наприклад, редирект)
        if (Auth::check()) {
            $this->message = 'You are already logged in. Use this link to register a new seller account.';
            return redirect()->route('home');
        }

        $this->user_info = USER::where('id', $this->invite->user_id)->first();

        if (!$this->user_info) {
            $this->message = 'This invitation link has already been used or has expired.';
            return;
        }
    }

    public function verifyTelegram($telegramData)
    {
        // Імітуємо відповідь від вашого AJAX-запиту
        $user = $telegramData;
        if (!isset($user['username'])) {
            $user['username'] = $user['id'];
        }

        $this->telegram_id = $user['id'];
        $this->telegram_username = $user['username'];
        $this->username = $user['username'];
        $this->first_name = $user['first_name'] ?? '';
        $this->last_name = $user['last_name'] ?? '';
        $this->telegramVerified = true;

        $this->dispatch('telegram-verified');
    }

    public function register()
    {

        if (!$this->telegramVerified) {
            $this->addError('telegram_id', 'Please verify your Telegram account.');
            $this->dispatch('show-error', message: 'Telegram verification required.');
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Повторно кидаємо виняток, щоб Livewire повернув помилки
        }

        if (!$this->invite || $this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'Invalid or used invitation link.';
            $this->dispatch('show-error', message: $this->message);
            return;
        }

        $user = User::create([
            'username' => $this->username,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'password' => Hash::make($this->password),
            'telegram_id' => $this->telegram_id,
            'telegram_username' => $this->telegram_username,
        ]);

        $this->invite->update(['status' => SellerInvite::STATUS_USED]);
        $this->dispatch('show-success', message: 'Registration successful! Welcome, ' . $this->username . '!');

        Auth::login($user);
        return redirect()->route('seller.dashboard');
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        if (!$this->telegramVerified) {
            $this->dispatch('reinit-telegram');
        }
        return view('livewire.elegant.sellers.seller-register');
    }
}
