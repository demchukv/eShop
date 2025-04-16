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

        // Функція для завантаження та відображення остаточного рішення
        document.addEventListener('DOMContentLoaded', () => {
            const finalDecisionDiv = document.getElementById('final-decision');
            const finalDecisionContent = document.getElementById('final-decision-content');
            const disputId = finalDecisionDiv.dataset.disputId;
            const currency = finalDecisionDiv.dataset.currency;

            fetch('{{ route('seller.disput.messages', $disput->id) }}')
                .then(response => response.json())
                .then(data => {
                    const acceptedMessage = data.messages.find(msg => msg.proposal_status === 'accepted');
                    if (acceptedMessage) {
                        finalDecisionContent.innerHTML = `
                            <p class="mb-0"><strong>Refund Amount::</strong> ${currency}${parseFloat(acceptedMessage.refund_amount).toFixed(2)}</p>
                            <p class="mb-0"><strong>Application Type:</strong> ${data.application_types[acceptedMessage.application_type] || acceptedMessage.application_type}</p>
                            <p class="mb-0"><strong>Refund method:</strong> ${data.refund_methods[acceptedMessage.refund_method] || acceptedMessage.refund_method}</p>
                            <p class="mb-0"><strong>Accepted:</strong> ${new Date(acceptedMessage.created_at).toLocaleString()}</p>
                        `;
                    } else {
                        finalDecisionContent.innerHTML = '<p class="mb-0">Остаточне рішення відсутнє.</p>';
                    }
                })
                .catch(error => {
                    console.error('Помилка при завантаженні повідомлень:', error);
                    finalDecisionContent.innerHTML = '<p class="mb-0">Помилка завантаження даних.</p>';
                });
        });
    </script>
@endsection
