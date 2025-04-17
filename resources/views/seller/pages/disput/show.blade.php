<?php
// Note: Blade templates are primarily HTML with PHP directives, so contentType is text/html
?>
@extends('seller/layout')

@section('title')
    {{ labels('admin_labels.disput_details', 'Disput Details') }}
@endsection

@section('content')
    <section class="main-content">
        <div class="row">
            <div class="d-flex row align-items-center">
                <div class="col-md-6 page-info-title">
                    <h3>{{ labels('admin_labels.disput_details', 'Disput Details') }}</h3>
                    <p class="sub_title">
                        {{ labels('admin_labels.all_information_about_disput', 'All Information About Disput') }}
                    </p>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <nav aria-label="breadcrumb" class="float-end">
                        <ol class="breadcrumb">
                            <i class='bx bx-home-smile'></i>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('seller.home') }}">{{ labels('admin_labels.home', 'Home') }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('seller.return_requests.index') }}">{{ labels('admin_labels.manage_return_requests', 'Return Requests') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ labels('admin_labels.disput_details', 'Disput Details') }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="overview-data">
            <div class="card content-area p-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="disput-details mb-4 d-flex gap-4">
                            <div>
                                <p class="mb-0"><strong>{{ labels('admin_labels.disput_id', 'Disput ID') }}:</strong>
                                    {{ $disput->id }}
                                </p>
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
                                    @php
                                        $statusConfig = config('return_requests.statuses')[
                                            $disput->returnRequest->status
                                        ] ?? ['label' => 'Unknown', 'badge' => 'bg-warning'];
                                    @endphp
                                    <span class="badge {{ $statusConfig['badge'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </p>
                                <!-- Форма зміни статусу -->
                                @if ($disput->seller_id === Auth::id())
                                    <form action="{{ route('seller.disput.updateReturnStatus', $disput->id) }}"
                                        method="POST" class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Change Return Status</label>
                                            <select class="form-select select2" id="status" name="status" required>
                                                <option value="">Select a status...</option>
                                                @foreach (['2' => 'Approved', '3' => 'Return Picked Up', '4' => 'Returned'] as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ $disput->returnRequest->status == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Status</button>
                                    </form>
                                @endif
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
                                                $isImage = in_array(strtolower($extension), [
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'gif',
                                                ]);
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
                            <div id="final-decision" data-disput-id="{{ $disput->id }}"
                                data-currency="{{ $currency }}" class="bg-light p-2">
                                <p class="mb-0 mt-3">
                                    <strong>{{ labels('admin_labels.final_decision', 'Final decision') }}:</strong>
                                </p>
                                <div id="final-decision-content">
                                    <!-- Контент буде заповнено через JavaScript -->
                                </div>
                                @if (
                                    $disput->seller_id === Auth::id() &&
                                        $disput->returnRequest->application_type === 'return_and_refund' &&
                                        ($disput->returnRequest->status == 2 ||
                                            ($disput->returnRequest->status == 3 &&
                                                $disput->returnRequest->orderTracking &&
                                                $disput->returnRequest->orderTracking->sender_id === Auth::id())))
                                    <form action="{{ route('seller.disput.submitTracking', $disput->id) }}" method="POST"
                                        class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="trackingNumber" class="form-label">Tracking Number</label>
                                            <input type="text" class="form-control" id="trackingNumber"
                                                name="tracking_number"
                                                value="{{ old('tracking_number', $disput->returnRequest->orderTracking->tracking_number ?? '') }}"
                                                required>
                                            @error('tracking_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="courierService" class="form-label">Courier Service</label>
                                            <select class="form-select select2" id="courierService" name="courier_service"
                                                required>
                                                <option value="">Select a courier...</option>
                                                @foreach ($couriers as $courier)
                                                    <option value="{{ $courier['slug'] }}"
                                                        {{ old('courier_service', $disput->returnRequest->orderTracking->courier_agency ?? '') === $courier['slug'] ? 'selected' : '' }}>
                                                        {{ $courier['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('courier_service')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit Tracking</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <x-disput-chat :disput="$disput" />

                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection

@section('scripts')
    <script type="module">
        import {
            initDisputChat
        } from '{{ asset('assets/admin/custom/disput-chat.js') }}';
        initDisputChat(
            '{{ route('seller.disput.messages', $disput->id) }}',
            '{{ route('seller.disput.send_message', $disput->id) }}',
            '{{ route('seller.disput.accept_proposal', ['id' => $disput->id, 'messageId' => ':messageId']) }}',
            '{{ route('seller.disput.submit_contrproposal', ['id' => $disput->id, 'messageId' => ':messageId']) }}',
            '{{ route('seller.disput.call_admin', ['id' => $disput->id, 'messageId' => ':messageId']) }}'
        );

        // Ініціалізація Select2 для форми трекінгу та статусу
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: function() {
                    return $(this).attr('id') === 'courierService' ? "Select a courier..." :
                        "Select a status...";
                },
                allowClear: true
            });
        });
    </script>
@endsection
