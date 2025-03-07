<div>
    <button wire:click="copyToClipboard"
        class="btn-sm p-1 {{ $buttonState === 'copied' ? 'btn-success' : 'btn-secondary' }}" wire:loading.attr="disabled"
        wire:key="copy-button-{{ $uniqueId }}">
        {{ $buttonState === 'copied' ? 'Copied!' : 'Copy' }}
    </button>

    {{-- @push('scripts') --}}
    <script>
        document.addEventListener('livewire:init', function() {
            window.addEventListener('copy-to-clipboard-{{ $uniqueId }}', function(event) {
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
    {{-- @endpush --}}

</div>
