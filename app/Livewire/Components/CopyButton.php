<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CopyButton extends Component
{
    public string $textToCopy;
    public string $buttonState = 'copy';
    public string $uniqueId;

    public function mount($text)
    {
        $this->textToCopy = $text;
        $this->uniqueId = uniqid('copy-button-');
    }

    public function copyToClipboard()
    {
        // Ця функція буде викликатися при кліку
        $this->dispatch($this->uniqueId . '_btn', text: $this->textToCopy, success: 'Copied to clipboard!');
    }

    public function render()
    {
        return view('livewire.elegant.components.copy-button');
    }
}
