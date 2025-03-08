<?php

namespace App\Livewire\Sellers;

use App\Models\SellerInvite;
use App\Models\User;
use Illuminate\Http\Request;
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
    public $friend_code = '';

    protected $rules = [
        'username' => 'required|string|max:255|unique:users,username',
        'mobile' => 'required|numeric|digits_between:9,15|unique:users,mobile',
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
        $this->invite = SellerInvite::where('link', $this->link)->first();

        if (!$this->invite || $this->invite->isExpired() || $this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'Invalid or expired invitation link.';
            return redirect()->route('seller.telegram.verify', ['link' => $this->link]);
        }

        $telegramData = session()->get('seller_telegram_data');
        if (!$telegramData || $telegramData['invite_link'] !== $this->link) {
            $this->message = 'Please verify your Telegram account first.';
            return redirect()->route('seller.telegram.verify', ['link' => $this->link]);
        }

        $this->telegram_id = $telegramData['telegram_id'];
        $this->telegram_username = $telegramData['telegram_username'];
        $this->username = $this->telegram_username; // Заповнюємо за замовчуванням
        $this->first_name = $telegramData['first_name'];
        $this->last_name = $telegramData['last_name'];
        $this->user_info = User::where('id', $this->invite->user_id)->first();
        $this->friend_code = $this->user_info->referral_code;
    }

    public function register(Request $request)
    {
        $this->dispatch('start-verification', message: 'Start verification!' . $request->phone_full);

        $this->validate();

        if (!$this->invite || $this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'Invalid or used invitation link.';
            $this->dispatch('show-error', message: $this->message);
            return;
        }

        // $user = User::create([
        //     'username' => $this->username,
        //     'mobile' => $this->mobile,
        //     'email' => $this->email,
        //     'first_name' => $this->first_name,
        //     'last_name' => $this->last_name,
        //     'password' => Hash::make($this->password),
        //     'telegram_id' => $this->telegram_id,
        //     'telegram_username' => $this->telegram_username,
        //     'friend_code' => $this->friend_code,
        // ]);

        // $this->invite->update(['status' => SellerInvite::STATUS_USED]);
        // $this->dispatch('show-error', message: 'Registration failed!');
        $this->dispatch('show-success', message: 'Registration successful!');

        // Auth::login($user);
        // return redirect()->route('seller');
        return;
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
