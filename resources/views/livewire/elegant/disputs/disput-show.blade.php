<div id="page-content">
    <div class="container-fluid h-100">
        <div class="row">
            <div class="col-12">
                <div class="container mt-4">
                    <h2>Disput for Return Request</h2>
                    <div class="disput-details mb-4 d-flex gap-4">
                        <div>
                            <p class="mb-0">
                                <strong>{{ labels('admin_labels.return_request_id', 'Return Request ID') }}:</strong>
                                {{ $disput->return_request_id }}
                            </p>
                            <p class="mb-0"><strong>{{ labels('admin_labels.order_id', 'Order ID') }}:</strong>
                                {{ $disput->returnRequest->order_id }}</p>
                            <p class="mb-0"><strong>{{ labels('admin_labels.user_name', 'User Name') }}:</strong>
                                {{ $disput->returnRequest->user->username }}
                                ({{ $disput->returnRequest->user->first_name }}
                                {{ $disput->returnRequest->user->last_name }})</p>
                            <p class="mb-0">
                                <strong>{{ labels('admin_labels.product_name', 'Product Name') }}:</strong>
                                {{ $disput->returnRequest->orderItem->product->name }}
                            </p>
                            <p class="mb-0"><strong>{{ labels('admin_labels.reason', 'Reason') }}:</strong>
                                {{ config('return_reasons')[$disput->returnRequest->reason] ?? $disput->returnRequest->reason }}
                            </p>
                            <p class="mb-0">
                                <strong>{{ labels('admin_labels.application_type', 'Application Type') }}:</strong>
                                {{ config('application_types')[$disput->returnRequest->application_type] ?? $disput->returnRequest->application_type }}
                            </p>
                            <p class="mb-0">
                                <strong>{{ labels('admin_labels.refund_amount', 'Refund Amount') }}:</strong>
                                {{ $currency . number_format($disput->returnRequest->refund_amount, 2) }}
                                ({{ config('refund_methods')[$disput->returnRequest->refund_method] ?? $disput->returnRequest->refund_method }})
                            </p>
                            <p class="mb-0 d-flex gap-2">
                                <strong>{{ labels('admin_labels.status', 'Status') }}:</strong>
                                <span
                                    class="badge {{ $disput->returnRequest->status == 0 ? 'bg-secondary' : ($disput->returnRequest->status == 1 ? 'bg-success' : 'bg-danger') }}">
                                    {{ $disput->returnRequest->status == 0 ? 'Pending' : ($disput->returnRequest->status == 1 ? 'Approved' : 'Declined') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            @if (!empty($disput->returnRequest->evidence_path))
                                <p class="mb-0 mt-3">
                                    <strong>{{ labels('admin_labels.evidence', 'Evidence') }}:</strong>
                                </p>
                                <div class="evidence-gallery d-flex flex-wrap gap-2 mt-2">
                                    @foreach ($disput->returnRequest->evidence_path as $path)
                                        @php
                                            $extension = pathinfo($path, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                            $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg']);
                                        @endphp
                                        @if ($isImage)
                                            <a href="{{ asset('storage/' . $path) }}"
                                                data-lightbox="evidence-{{ $disput->id }}">
                                                <img src="{{ asset('storage/' . $path) }}" alt="Evidence"
                                                    style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                            </a>
                                        @elseif ($isVideo)
                                            <video width="200" controls>
                                                <source src="{{ asset('storage/' . $path) }}"
                                                    type="video/{{ $extension }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="mb-0 mt-3">
                                    <strong>{{ labels('admin_labels.evidence', 'Evidence') }}:</strong> No
                                    evidence provided.
                                </p>
                            @endif
                        </div>
                    </div>
                    <!-- Контейнер для повідомлень -->
                    <div class="disput-container" wire:poll.10s="loadMessages"
                        style="border: 1px solid #ddd; padding: 15px; height: 400px; overflow-y: auto; margin-bottom: 20px;">
                        @forelse ($messages as $message)
                            <div
                                class="message {{ $message['sender_id'] === Auth::id() ? 'text-end' : 'text-start' }} mb-2">
                                <div class="d-inline-block p-2 rounded"
                                    style="background: {{ $message['sender_id'] === Auth::id() ? '#d1e7dd' : '#f8d7da' }}; max-width: 70%;">
                                    <strong>{{ $message['sender_name'] ?? 'Anonymous' }}</strong>:
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
    </script>
@endpush
