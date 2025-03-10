<div>
    <button wire:click="copyToClipboard" class="btn-lg p-0 {{ $buttonState === 'copied' ? 'btn-link' : 'btn-link' }}"
        wire:loading.attr="disabled" wire:key="{{ $uniqueId }}">
        <ion-icon name="copy-outline"></ion-icon>
    </button>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', function() {
                window.addEventListener('{{ $uniqueId }}_btn', function(event) {
                    const text = event.detail.text;
                    navigator.clipboard.writeText(text).then(() => {
                        @this.set('buttonState', 'copied');
                        setTimeout(() => {
                            @this.set('buttonState', 'copy');
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                });
            });
        </script>
    @endpush

</div>
