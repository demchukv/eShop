<?php

namespace App\Livewire;

use Livewire\Component;

class DateInput extends Component
{
    public $name;
    public $id;
    public $placeholder;

    public function mount($name = null, $id = null, $placeholder = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->placeholder = $placeholder;
    }

    public function render()
    {

        return view('livewire.date-input');
    }

    protected function rules()
    {
        return [
            'birthdate' => 'required|date',
        ];
    }
}
