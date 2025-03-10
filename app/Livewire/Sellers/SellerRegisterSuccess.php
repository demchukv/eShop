<?php

namespace App\Livewire\Sellers;

use Livewire\Component;

class SellerRegisterSuccess extends Component
{
    public $username;

    public function mount()
    {
        // Перевіряємо, чи є дані про успішну реєстрацію в сесії
        $registrationData = session()->get('seller_registration_success');

        if (!$registrationData || !isset($registrationData['username'])) {
            // Якщо даних немає, перенаправляємо назад (наприклад, на головну)
            return redirect()->route('home')->with('error', 'Unauthorized access to success page.');
        }

        $this->username = $registrationData['username'];

        // Очищаємо сесію після використання (опціонально)
        session()->forget('seller_registration_success');
    }

    public function render()
    {
        return view('livewire.elegant.sellers.seller-register-success');
    }
}
