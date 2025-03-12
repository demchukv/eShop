<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CopyButton extends Component
{
    public string $textToCopy;

    public function mount($text)
    {
        $this->textToCopy = $text;
    }

    public function render()
    {
        return view('livewire.elegant.components.copy-button');
    }
}
