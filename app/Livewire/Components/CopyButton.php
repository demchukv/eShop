<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CopyButton extends Component
{
    public string $textToCopy;
    public string $buttonState = 'copy';

    public function mount($text)
    {
        $this->textToCopy = $text;
    }

    public function copyToClipboard()
    {
        // Ця функція буде викликатися при кліку
        $this->dispatch('copy-to-clipboard', text: $this->textToCopy, success: 'Copied to clipboard!');
        $this->buttonState = 'copied';

        // Опціонально: повернути текст кнопки до початкового стану через 2 секунди
        $this->resetButtonText();
    }

    private function resetButtonText()
    {
        sleep(1);
        $this->buttonState = 'copy';
    }

    public function render()
    {
        return view('livewire.elegant.components.copy-button');
    }
}
