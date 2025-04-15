@extends('admin/layout')

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
                                    href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.return_requests.index') }}">{{ labels('admin_labels.manage_return_requests', 'Return Requests') }}</a>
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
                <div class="row align-items-center d-flex heading mb-5">
                    <div class="col-md-12">
                        <h4>{{ labels('admin_labels.disput_conversation', 'Disput Conversation') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="disput-details mb-4">
                            <h5>{{ labels('admin_labels.disput_information', 'Disput Information') }}</h5>
                            <p><strong>{{ labels('admin_labels.disput_id', 'Disput ID') }}:</strong> {{ $disput->id }}
                            </p>
                            <p><strong>{{ labels('admin_labels.return_request_id', 'Return Request ID') }}:</strong>
                                {{ $disput->return_request_id }}</p>
                            <p><strong>{{ labels('admin_labels.order_id', 'Order ID') }}:</strong>
                                {{ $disput->returnRequest->order_id }}</p>
                            <p><strong>{{ labels('admin_labels.user_name', 'User Name') }}:</strong>
                                {{ $disput->returnRequest->user->username }}</p>
                            <p><strong>{{ labels('admin_labels.product_name', 'Product Name') }}:</strong>
                                {{ $disput->returnRequest->orderItem->product->name }}</p>
                            <p><strong>{{ labels('admin_labels.reason', 'Reason') }}:</strong>
                                {{ config('return_reasons')[$disput->returnRequest->reason] ?? $disput->returnRequest->reason }}
                            </p>
                            <p><strong>{{ labels('admin_labels.refund_amount', 'Refund Amount') }}:</strong>
                                {{ $currency . number_format($disput->returnRequest->refund_amount, 2) }}</p>
                            <p><strong>{{ labels('admin_labels.status', 'Status') }}:</strong>
                                <span
                                    class="badge {{ $disput->returnRequest->status == 0 ? 'bg-secondary' : ($disput->returnRequest->status == 1 ? 'bg-success' : 'bg-danger') }}">
                                    {{ $disput->returnRequest->status == 0 ? 'Pending' : ($disput->returnRequest->status == 1 ? 'Approved' : 'Declined') }}
                                </span>
                            </p>
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
            '{{ route('admin.disput.messages', $disput->id) }}',
            '{{ route('admin.disput.send_message', $disput->id) }}'
        );
    </script>
@endsection
