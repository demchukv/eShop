<?php

namespace App\Livewire\Sellers;

use App\Models\SellerInvite;
use App\Models\User;
use Livewire\Component;
// use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

class SellerRegister extends Component
{
    // use WithFileUploads;
    use WithFilePond;

    public $file;

    public $link;
    public $invite;
    public $message = '';
    public $user_info;
    public $telegram_id = '';
    public $telegram_username = '';
    public $username = '';
    public $mobile = '';
    public $phone_full = '';
    public $country_code = '';
    public $email = '';
    public $first_name = '';
    public $last_name = '';
    public $password = '';
    public $confirm_password = '';
    public $address = '';
    public $agree = false;
    public $friend_code = '';

    public $account_number = '';
    public $account_name = '';
    public $bank_name = '';
    public $bank_code = '';

    public $store_name = '';
    public $store_url = '';
    public $description = '';

    public $city = '';
    public $zipcode = '';

    public $tax_name = '';
    public $tax_number = '';
    public $pan_number = '';
    public $latitude = '';
    public $longitude = '';

    public $profile_image = null;
    public $address_proof = null;
    public $authorized_signature = null;

    protected $rules = [
        'username' => 'required|string|max:255|unique:users,username',
        'mobile' => 'required|numeric|digits_between:8,15|unique:users,mobile',
        'email' => 'required|email|max:255|unique:users,email',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'password' => 'required|string|min:8|confirmed',
        'address' => 'required|string|min:10',
        'agree' => 'accepted',
        'account_number' => 'required|string|min:10',
        'account_name' => 'required|string|min:10',
        'bank_name' => 'required|string|min:10',
        'bank_code' => 'required|string|min:5',
        'store_name' => 'required|string|min:10',
        'store_url' => 'required|string|min:5',
        'description' => 'required|string|min:10',

        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'address_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'authorized_signature' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

    public function updated($propertyName)
    {
        if ($propertyName === 'profile_image') {
            // Обробка файлу profile_image
            $this->profile_image = $this->profile_image->store('example', 'public');
        }
        if ($propertyName === 'address_proof') {
            // Обробка файлу address_proof
            $this->address_proof = $this->address_proof->store('example', 'public');
        }
        if ($propertyName === 'authorized_signature') {
            // Обробка файлу authorized_signature
            $this->authorized_signature = $this->authorized_signature->store('example', 'public');
        }
        if ($propertyName === 'file') {
            // Обробка файлу authorized_signature
            $this->file = $this->file->store('example', 'public');
        }
    }

    public function register()
    {

        $this->validate();

        if (!$this->invite || $this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'Invalid or used invitation link.';
            $this->dispatch('show-error', message: $this->message);
            return;
        }

        $this->profile_image->store('example', 'public');
        $this->address_proof->store('example', 'public');
        $this->authorized_signature->store('example', 'public');

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


    public function render()
    {
        return view('livewire.elegant.sellers.seller-register');
    }
}
