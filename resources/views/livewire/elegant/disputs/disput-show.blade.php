<?php
// Note: Blade templates are primarily HTML with PHP directives, so contentType is text/html
?>
@props(['disput', 'messages', 'currency', 'couriers'])

<div>
    <div class="card content-area p-4">
        <div class="row">
            <div class="col-md-12">
                <div class="disput-details mb-4 d-flex gap-4">
                    <div>
                        <p class="mb-0"><strong>{{ labels('admin_labels.disput_id', 'Disput ID') }}:</strong>
                            {{ $disput->id }}</p>
                        <p class="mb-0">
                            <strong>{{ labels('admin_labels.return_request_id', 'Return Request ID') }}:</strong>
                            {{ $disput->return_request_id }}
                        </p>
                        <p class="mb-0"><strong>{{ labels('admin_labels.order_id', 'Order ID') }}:</strong>
                            {{ $disput->returnRequest->order_id }}</p>
                        <p class="mb-0"><strong>{{ labels('admin_labels.user_name', 'User Name') }}:</strong>
                            {{ $disput->returnRequest->user->username }} ({{ $disput->returnRequest->user->first_name }}
                            {{ $disput->returnRequest->user->last_name }})</p>
                        <p class="mb-0"><strong>{{ labels('admin_labels.product_name', 'Product Name') }}:</strong>
                            {{ $disput->returnRequest->orderItem->product->name }}</p>
                        <p class="mb-0"><strong>{{ labels('admin_labels.reason', 'Reason') }}:</strong>
                            {{ config('return_reasons')[$disput->returnRequest->reason] ?? $disput->returnRequest->reason }}
                        </p>
                        <p class="mb-0">
                            <strong>{{ labels('admin_labels.application_type', 'Application Type') }}:</strong>
                            {{ config('application_types')[$disput->returnRequest->application_type] ?? $disput->returnRequest->application_type }}
                        </p>
                        <p class="mb-0"><strong>{{ labels('admin_labels.refund_amount', 'Refund Amount') }}:</strong>
                            {{ $currency . number_format($disput->returnRequest->refund_amount, 2) }}
                            ({{ config('refund_methods')[$disput->returnRequest->refund_method] ?? $disput->returnRequest->refund_method }})
                        </p>
                        <p class="mb-0 d-flex gap-2"><strong>{{ labels('admin_labels.status', 'Status') }}:</strong>
                            <span
                                class="badge {{ $disput->returnRequest->status == 0 ? 'bg-secondary' : ($disput->returnRequest->status == 1 ? 'bg-danger' : ($disput->returnRequest->status == 2 ? 'bg-success' : ($disput->returnRequest->status == 3 ? 'bg-info' : 'bg-primary'))) }}">
                                {{ $disput->returnRequest->status == 0 ? 'Pending' : ($disput->returnRequest->status == 1 ? 'Rejected' : ($disput->returnRequest->status == 2 ? 'Approved' : ($disput->returnRequest->status == 3 ? 'Return Picked Up' : 'Returned'))) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        @if (!empty($disput->returnRequest->evidence_path))
                            <p class="mb-0 mt-3"><strong>{{ labels('admin_labels.evidence', 'Evidence') }}:</strong>
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
                            <p class="mb-0 mt-3"><strong>{{ labels('admin_labels.evidence', 'Evidence') }}:</strong> No
                                evidence provided.</p>
                        @endif
                    </div>
                    <div>
                        {{-- @php
                            dd($disput->ReturnRequest);
                        @endphp --}}
                        @if ($disput->returnRequest->status > 1)
                            <div class="bg-light p-2 mt-3" id="final-decision">
                                <p class="mb-0">
                                    <strong>{{ labels('admin_labels.final_decision', 'Final Decision') }}:</strong>
                                </p>
                                <p class="mb-0"><strong>Refund Amount:</strong>
                                    {{ $currency . number_format($disput->returnRequest->refund_amount, 2) }}</p>
                                <p class="mb-0"><strong>Application Type:</strong>
                                    {{ config('application_types')[$disput->returnRequest->application_type] ?? $disput->returnRequest->application_type }}
                                </p>
                                <p class="mb-0"><strong>Refund Method:</strong>
                                    {{ config('refund_methods')[$disput->returnRequest->refund_method] ?? $disput->returnRequest->refund_method }}
                                </p>
                                @if ($disput->returnRequest->status == 2)
                                    <p class="mb-0"><strong>Accepted At:</strong>
                                        {{ \Carbon\Carbon::parse($disput->returnRequest->updated_at)->toDateTimeString() }}
                                    </p>
                                @endif
                                @if ($disput->returnRequest->orderTracking)
                                    <p class="mb-0"><strong>Tracking Number:</strong>
                                        {{ $disput->returnRequest->orderTracking->tracking_number }}</p>
                                    <p class="mb-0"><strong>Courier Service:</strong>
                                        {{ $disput->returnRequest->orderTracking->courier_agency }}</p>
                                    @if ($disput->returnRequest->orderTracking->url)
                                        <p class="mb-0"><strong>Tracking URL:</strong>
                                            <a href="{{ $disput->returnRequest->orderTracking->url }}"
                                                target="_blank">Track</a>
                                        </p>
                                    @endif
                                    {{-- <p class="mb-0"><strong>Status:</strong>
                                        {{ ucfirst(str_replace('_', ' ', $disput->returnRequest->orderTracking->status)) }}
                                    </p> --}}
                                @endif
                                @if (
                                    $disput->user_id === Auth::id() &&
                                        $disput->returnRequest->application_type === 'return_and_refund' &&
                                        $disput->returnRequest->status == 2)
                                    <form wire:submit.prevent="submitTracking" class="mt-3">
                                        <div class="mb-3">
                                            <label for="trackingNumber" class="form-label">Tracking Number</label>
                                            <input type="text" class="form-control" id="trackingNumber"
                                                wire:model="tracking.tracking_number" required>
                                            @error('tracking.tracking_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="courierService" class="form-label">Courier Service</label>
                                            <select class="form-select select2" id="courierService"
                                                wire:model="tracking.courier_service" required>
                                                <option value="">Select a courier...</option>
                                                @foreach ($couriers as $courier)
                                                    <option value="{{ $courier['slug'] }}">{{ $courier['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tracking.courier_service')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit Tracking</button>
                                        @error('form')
                                            <span class="text-danger d-block mt-2">{{ $message }}</span>
                                        @enderror
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="disput-chat">
                    <h5>{{ labels('admin_labels.chat_history', 'Chat History') }}</h5>
                    <div class="chat-container mb-4" wire:poll.10s="loadMessages"
                        style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                        @foreach ($messages as $message)
                            @php
                                $isAdmin = $message['sender_type'] === 'admin';
                                $isSeller = $message['sender_type'] === 'seller';
                                $messageClass = $isAdmin || $isSeller ? 'text-end' : 'text-start';
                                $messageBg = $isAdmin ? 'bg-primary' : ($isSeller ? 'bg-success' : 'bg-light');

                                $canRespond =
                                    ($message['sender_id'] === $disput->seller_id && $disput->user_id === Auth::id()) ||
                                    ($message['sender_id'] === $disput->user_id && $disput->seller_id === Auth::id());
                            @endphp

                            <div class="mb-2 {{ $messageClass }}">
                                <div class="p-2 rounded d-inline-block {{ $messageBg }}">
                                    <strong>{{ $message['sender_name'] }}:</strong>
                                    @if ($message['proposal_status'] === 'open' || $message['proposal_status'] === 'counter')
                                        <p><strong>Proposed:</strong>
                                            {{ config('application_types')[$message['application_type']] ?? $message['application_type'] }}
                                            for {{ $currency }}{{ number_format($message['refund_amount'], 2) }}
                                            to
                                            {{ config('refund_methods')[$message['refund_method']] ?? $message['refund_method'] }}
                                        </p>
                                        @if ($message['message'])
                                            <p><strong>Message:</strong> {{ $message['message'] }}</p>
                                        @endif
                                        @if (!empty($message['evidence_path']) && is_array($message['evidence_path']))
                                            <div class="evidence-gallery d-flex flex-wrap gap-2 mt-2">
                                                @foreach ($message['evidence_path'] as $path)
                                                    @php
                                                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), [
                                                            'jpg',
                                                            'jpeg',
                                                            'png',
                                                            'gif',
                                                        ]);
                                                        $isVideo = in_array(strtolower($extension), [
                                                            'mp4',
                                                            'webm',
                                                            'ogg',
                                                        ]);
                                                    @endphp
                                                    @if ($isImage)
                                                        <a href="{{ asset('storage/' . $path) }}"
                                                            data-lightbox="evidence-{{ $message['id'] }}">
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
                                        @endif
                                    @elseif ($message['proposal_status'] === 'accepted')
                                        <p><strong>Proposed:</strong>
                                            {{ config('application_types')[$message['application_type']] ?? $message['application_type'] }}
                                            for {{ $currency }}{{ number_format($message['refund_amount'], 2) }}
                                            to
                                            {{ config('refund_methods')[$message['refund_method']] ?? $message['refund_method'] }}
                                        </p>
                                        <p>Proposal accepted.</p>
                                    @elseif ($message['proposal_status'] === 'admin_call')
                                        <p>Admin intervention requested.</p>
                                    @elseif ($message['proposal_status'] === 'tracking_submitted')
                                        <p>{{ $message['message'] }}</p>
                                    @else
                                        <p>{{ $message['message'] }}</p>
                                    @endif
                                    <small>{{ \Carbon\Carbon::parse($message['created_at'])->toDateTimeString() }}</small>

                                    @if ($message['proposal_status'] === 'open' && $disput->status === 'open' && $canRespond)
                                        <div class="mt-2">
                                            <button wire:click="acceptProposal({{ $message['id'] }})"
                                                class="btn btn-sm btn-success me-2">Accept</button>
                                            <button wire:click="openContrproposalModal({{ $message['id'] }})"
                                                class="btn btn-sm btn-primary me-2">Contrproposal</button>
                                            <button wire:click="callAdmin({{ $message['id'] }})"
                                                class="btn btn-sm btn-warning">Call Admin</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($disput->status === 'open')
                        <div class="chat-input">
                            <form wire:submit.prevent="sendMessage">
                                <div class="input-group">
                                    <textarea wire:model="newMessage" class="form-control"
                                        placeholder="{{ labels('admin_labels.type_message', 'Type your message...') }}" required></textarea>
                                    <button type="submit"
                                        class="btn btn-primary">{{ labels('admin_labels.send', 'Send') }}</button>
                                </div>
                                @error('newMessage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Contrproposal Modal -->
    <div class="modal fade" id="contrproposalModal" tabindex="-1" aria-labelledby="contrproposalModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contrproposalModalLabel">Make a Counterproposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="refundAmount" class="form-label">Refund Amount</label>
                            <input type="number" class="form-control" id="refundAmount"
                                wire:model="contrproposal.refund_amount" step="0.01" min="0" required>
                            @error('contrproposal.refund_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="applicationType" class="form-label">Application Type</label>
                            <select class="form-select" id="applicationType"
                                wire:model="contrproposal.application_type" required>
                                <option value="">Select an option...</option>
                                @foreach (config('application_types') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('contrproposal.application_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="refundMethod" class="form-label">Refund Method</label>
                            <select class="form-select" id="refundMethod" wire:model="contrproposal.refund_method"
                                required>
                                <option value="">Select an option...</option>
                                @foreach (config('refund_methods') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('contrproposal.refund_method')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" wire:model="contrproposal.message" rows="4" required></textarea>
                            @error('contrproposal.message')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="evidence" class="form-label">Upload Evidence</label>
                            <input type="file" class="form-control" id="evidence"
                                wire:model="contrproposal.evidence" multiple accept="image/*,video/*">
                            @error('contrproposal.evidence.*')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="submitContrproposal">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Ініціалізація Select2
                $('#courierService').select2({
                    placeholder: "Select a courier...",
                    allowClear: true
                });

                // Синхронізація Select2 із Livewire
                $('#courierService').on('change', function(e) {
                    @this.set('tracking.courier_service', e.target.value);
                });

                Livewire.on('openContrproposalModal', () => {
                    let modal = new bootstrap.Modal(document.getElementById('contrproposalModal'));
                    modal.show();
                });

                Livewire.on('closeContrproposalModal', () => {
                    let modal = bootstrap.Modal.getInstance(document.getElementById('contrproposalModal'));
                    if (modal) {
                        modal.hide();
                    }
                });

                // Резервний асинхронний запит до /aftership/couriers
                async function loadCouriers() {
                    try {
                        const response = await fetch('/aftership/couriers', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        const data = await response.json();
                        console.log('Couriers response:', data);
                        const select = document.getElementById('courierService');
                        if (select && data && data.couriers) {
                            console.log(data.couriers);
                            select.innerHTML = '<option value="">Select a courier...</option>';
                            data.couriers.forEach(courier => {
                                const option = document.createElement('option');
                                option.value = courier.slug;
                                option.textContent = courier.name;
                                select.appendChild(option);
                            });
                            // Оновити Select2 після зміни опцій
                            $(select).trigger('change');
                        } else {
                            console.warn('No couriers found or select element missing', {
                                selectExists: !!select,
                                hasData: !!data,
                                hasCouriers: !!(data && data.couriers)
                            });
                        }
                    } catch (error) {
                        console.error('Error loading couriers:', error.message);
                        if (error.message.includes('404')) {
                            console.error('Route /aftership/couriers not found. Please check routes/web.php.');
                        }
                    }
                }

                if (document.getElementById('courierService')) {
                    const select = document.getElementById('courierService');
                    if (select.options.length <= 1) {
                        console.log('No server-side couriers, loading via fetch');
                        loadCouriers();
                    }
                }
            });
        </script>
    @endpush
</div>
