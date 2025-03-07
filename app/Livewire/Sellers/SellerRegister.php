<?php

namespace App\Livewire\Sellers;

use App\Models\SellerInvite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class SellerRegister extends Component
{


    public string $link;
    public ?SellerInvite $invite = null;
    public string $message = '';
    public $user_info;


    // Публічні властивості для полів форми
    public string $telegram_id = '';
    public string $telegram_username = '';
    public string $username = '';
    public string $mobile = '';
    public string $email = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree = false;

    // Правила валідації
    protected $rules = [
        'telegram_id' => 'required|string',
        'telegram_username' => 'required|string',
        'username' => 'required|string|max:255|unique:users,username',
        'mobile' => 'required|numeric|digits_between:10,15|unique:users,mobile',
        'email' => 'required|email|max:255|unique:users,email',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'password' => 'required|string|min:8|confirmed',
        'agree' => 'accepted', // Повинен бути true (чекбокс відмічений)
    ];

    // Кастомні повідомлення (опціонально)
    protected $messages = [
        'agree.accepted' => 'You must agree to the terms and policy.',
        'password.confirmed' => 'The password confirmation does not match.',
    ];

    public function mount($link)
    {
        $this->link = $link;

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

    public function register()
    {
        // Валідація даних
        $this->validate();

        if (!$this->invite) {
            $this->message = 'Invalid invitation link.';
            return;
        }

        if ($this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'This link is no longer valid.';
            return;
        }

        // Створення нового користувача
        // $user = User::create([
        //     'username' => $this->username,
        //     'mobile' => $this->mobile,
        //     'email' => $this->email,
        //     'first_name' => $this->first_name,
        //     'last_name' => $this->last_name,
        //     'password' => Hash::make($this->password),
        //     'telegram_id' => $this->telegram_id,
        //     'telegram_username' => $this->telegram_username,
        //     // Додайте інші поля, якщо потрібно (наприклад, роль продавця)
        // ]);

        // Оновлення статусу запрошення
        // $this->invite->update(['status' => SellerInvite::STATUS_USED]);

        // Повідомлення про успіх
        $this->dispatch('show-success', message: 'Registration successful! Welcome, ' . $this->username . '!');

        // Авторизація користувача (опціонально)
        // Auth::login($user);

        // Редирект на дашборд продавця
        // return redirect()->route('seller.dashboard');
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.elegant.sellers.seller-register');
    }
}
