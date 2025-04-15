<div id="page-content">
    <div class="container-fluid h-100">
        <div class="row">
            <div class="col-12">
                <div class="container mt-4">
                    <h2>Disput for Return Request #{{ $disput ? $disput->return_request_id : 'Unknown' }}</h2>

                    <!-- Контейнер для повідомлень -->
                    <div class="disput-container"
                        style="border: 1px solid #ddd; padding: 15px; height: 400px; overflow-y: auto; margin-bottom: 20px;">
                        @forelse ($messages as $message)
                            <div
                                class="message {{ $message['sender_id'] === Auth::id() ? 'text-end' : 'text-start' }} mb-2">
                                <div class="d-inline-block p-2 rounded"
                                    style="background: {{ $message['sender_id'] === Auth::id() ? '#d1e7dd' : '#f8d7da' }}; max-width: 70%;">
                                    <strong>{{ $message['sender']['name'] ?? 'Anonymous' }}</strong>:
                                    {{ $message['message'] }}
                                    <br>
                                    <small>{{ \Carbon\Carbon::parse($message['created_at'])->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                        @empty
                            <p>No messages yet.</p>
                        @endforelse
                    </div>

                    <!-- Форма для відправки повідомлення -->
                    <form wire:submit.prevent="sendMessage">
                        <div class="input-group">
                            <textarea wire:model.live="newMessage" class="form-control" rows="2" placeholder="Type your message..."></textarea>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                        @error('newMessage')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:updated', () => {
            // Прокручування донизу контейнера з повідомленнями
            const disputContainer = document.querySelector('.disput-container');
            if (disputContainer) {
                disputContainer.scrollTop = disputContainer.scrollHeight;
            }
        });

        // Періодичне оновлення повідомлень (кожні 10 секунд)
        setInterval(() => {
            @this.call('loadMessages');
        }, 10000);
    </script>
@endpush
