
<div>
    <button
        type="button"
        wire:click="copyToClipboard"
        class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center p-1"
        title="{{ $buttonState === 'copy' ? 'Copy to clipboard' : 'Copied' }}"
    >
       <ion-icon name="copy"></ion-icon>
    </button>

    {{-- @push('scripts') --}}
    <script>
        document.addEventListener('livewire:init', function () {
                window.addEventListener('copy-to-clipboard', function (event) {
                    console.log(event);
                    navigator.clipboard.writeText(event.detail.text)
                        .then(() => {
                            console.log('Текст скопійовано в буфер обміну: ' + event.detail.text);
                            alert(event.detail.success);
                        })
                        .catch(err => {
                            console.error('Помилка копіювання: ', err);
                        });
                });
            });
    </script>
    {{-- @endpush --}}

</div>
