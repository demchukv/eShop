<?php

namespace App\Livewire;

use Livewire\Component;

class PhoneInput extends Component
{
    public $mobile = '';
    public $country_code = '';
    public $phone_full = '';

    protected $rules = [
        'mobile' => 'required|numeric|digits_between:10,15',
        'country_code' => 'required|string',
        'phone_full' => 'required|string',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.phone-input');
    }
}
