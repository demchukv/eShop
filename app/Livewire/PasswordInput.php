<?php

namespace App\Livewire;

use Livewire\Component;

class PasswordInput extends Component
{
    public $password = '';
    public $confirm_password = '';
    public $showPassword = false;
    public $showConfirmPassword = false;

    protected $rules = [
        'password' => 'required|string|min:8',
        'confirm_password' => 'required|string|same:password',
    ];

    protected $messages = [
        'confirm_password.same' => 'The password confirmation does not match.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function togglePasswordVisibility($field)
    {
        if ($field === 'password') {
            $this->showPassword = !$this->showPassword;
        } elseif ($field === 'confirm_password') {
            $this->showConfirmPassword = !$this->showConfirmPassword;
        }
    }

    public function render()
    {
        return view('livewire.password-input');
    }
}
