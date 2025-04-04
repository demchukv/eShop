{{-- @dd($order_transaction); --}}

<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo />
    <div class="container-fluid h-100">
        <div class="orders-card mt-0 h-100">
            <div class="top-sec d-flex-justify-center justify-content-between mb-4">
                <h2 class="mb-0">{{ labels('front_messages.orders_details', 'Orders Details') }}</h2>
                <a href="{{ route('front_end.orders.generatInvoicePDF', $user_orders['order_data'][0]->id) }}"
                    class="btn btn-primary btn-sm instructions_files"><i
                        class='bx bx-download me-1'></i>{{ labels('admin_labels.invoice', 'Invoice') }}
                </a>
            </div>
            <div class="row mt-2">
                <div class="col-sm-12">
                    <h3>{{ labels('front_messages.order_id', 'order ID') }}: #{{ $user_orders['order_data'][0]->id }}
                    </h3>
                    @php
                        $currency_symbol = $user_orders['order_data'][0]->order_payment_currency_code;
                        $currency_details = fetchDetails('currencies', [
                            'symbol' => $currency_symbol,
                        ]);
                    @endphp
                    @foreach ($order_transaction as $user_order)
                        @foreach ($user_order['order_items'] as $user_order_item)
                            <div class="row mt-3">
                                <div class="col-lg-2 col-md-3 col-sm-4">
                                    <div class="product-img mb-3 mb-sm-0 order-image-box">
                                        @php
                                            $order_image = dynamic_image($user_order_item['image_sm'], 230);
                                        @endphp
                                        <a wire:navigate
                                            href="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}"
                                            class="all-product-img product-img rounded-3 slider-link"
                                            data-link="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}">
                                            <img class="rounded-0 blur-up lazyload" data-src="{{ $order_image }}"
                                                src="{{ $order_image }}" alt="{{ $user_order_item['product_name'] }}"
                                                title="{{ $user_order_item['product_name'] }}">
                                        </a>

                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-9 col-sm-8">
                                    <div class="tracking-detail d-flex-center">
                                        <ul>
                                            <li>
                                                <div class="left">
                                                    <span>{{ labels('front_messages.product_name', 'Product name') }}</span>
                                                </div>
                                                <div class="right">
                                                    <a wire:navigate
                                                        href="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}"
                                                        class="all-product-img product-img rounded-3 slider-link"
                                                        data-link="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}">
                                                        <span>{{ $user_order_item['product_name'] . ($user_order_item['variant_name'] ? ' - ' . $user_order_item['variant_name'] : '') }}</span>

                                                    </a>
                                                </div>
                                            </li>
                                            @if ($user_order_item['is_cancelable'] == 1)
                                                <li>
                                                    <div class="left">
                                                        <span>{{ labels('front_messages.cancelable_till', 'Cancelable Till') }}</span>
                                                    </div>
                                                    <div class="right">
                                                        <span>{{ $user_order_item['cancelable_till'] }}</span>
                                                    </div>
                                                </li>
                                            @endif
                                            <li>
                                                <div class="left">
                                                    <span>{{ labels('front_messages.total_price', 'total Price') }}</span>
                                                </div>
                                                <div class="right">
                                                    <span>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order_item['price'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="left">
                                                    <span>{{ labels('front_messages.final_total', 'Final Total') }}</span>
                                                </div>
                                                <div class="right"><b
                                                        class="text-black">{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order_item['sub_total'] * $currency_details[0]->exchange_rate, 2) : '' }}</b>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    @php
                                        $status = [
                                            'awaiting',
                                            'received',
                                            'processed',
                                            'shipped',
                                            'delivered',
                                            'cancelled',
                                            'return_request_pending',
                                            'return_request_decline',
                                            'returned',
                                        ];
                                        $cancelable_till = $user_order_item['cancelable_till'];
                                        $active_status = $user_order_item['active_status'];
                                        $cancellable_index = array_search($cancelable_till, $status);
                                        $active_index = array_search($active_status, $status);
                                    @endphp

                                    @php
                                        $max_days_to_return_item = $system_settings['max_days_to_return_item'] ?? 0;

                                        $deliveredTime = '';
                                        $is_return_time_is_over = false;

                                        if ($user_order_item['active_status'] == 'delivered') {
                                            foreach ($user_order_item['status'] as $status) {
                                                if ($status[0] == 'delivered') {
                                                    $deliveredTime = $status[1];
                                                }
                                            }
                                            // dd($user_order_item['status']);
                                            // if ($user_order_item['status'][3][0] == 'delivered') {
                                            //     $deliveredTime = $user_order_item['status'][3][1];
                                            // }
                                            // Перевірка, чи є $deliveredTime валідним
                                            if ($deliveredTime) {
                                                $deliveredDateTime = DateTime::createFromFormat(
                                                    'd-m-Y h:i:sa',
                                                    $deliveredTime,
                                                );

                                                // Перевірка, чи успішно створено об’єкт DateTime
                                                if ($deliveredDateTime !== false) {
                                                    $returnDeadline = $deliveredDateTime->modify(
                                                        '+' . $max_days_to_return_item . ' days',
                                                    );
                                                    $currentDateTime = new DateTime();
                                                    if ($currentDateTime < $returnDeadline) {
                                                        $is_return_time_is_over = true;
                                                    }
                                                } else {
                                                    $is_return_time_is_over = false; // Якщо парсинг не вдався
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex">
                                        @if (
                                            $user_order_item['is_already_cancelled'] == 0 &&
                                                $user_order_item['is_cancelable'] == 1 &&
                                                $cancellable_index >= $active_index)
                                            <button class="btn btn-primary btn-sm update_order_item_status"
                                                data-status="cancelled"
                                                data-item-id="{{ $user_order_item['id'] }}">{{ labels('front_messages.cancle', 'Cancel') }}</button>
                                        @endif
                                        @if (
                                            $user_order_item['is_returnable'] == 1 &&
                                                $user_order_item['return_request_submitted'] != 1 &&
                                                $user_order_item['active_status'] == 'delivered' &&
                                                $is_return_time_is_over == true)
                                            <button class="btn btn-primary btn-sm update_order_item_status"
                                                data-status="returned"
                                                data-item-id="{{ $user_order_item['id'] }}">{{ labels('front_messages.return', 'Return') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- End Status Order -->
                            <!-- Tracking Steps -->
                            <div class="tracking-steps nav mt-5 mb-4 clearfix justify-content-center">
                                {{-- @dd($user_order_item) --}}
                                @if ($user_order_item['active_status'] == 'cancelled')
                                    @foreach ($user_order_item['status'] as $status)
                                        <div class="step {{ $status[0] == 'cancelled' ? 'current' : '' }}">
                                            <span>{{ $status[0] }}</span>
                                        </div>
                                    @endforeach
                                @elseif (
                                    $user_order_item['is_already_returned'] == 1 ||
                                        $user_order_item['return_request_submitted'] >= 1 ||
                                        $user_order_item['active_status'] == 'return_request_pending')
                                    @foreach ($user_order_item['status'] as $status)
                                        <div
                                            class="step {{ $status[0] == $user_order_item['active_status'] ? 'current' : '' }}">
                                            <span>{{ str_replace('_', ' ', $status[0]) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div
                                        class="step {{ $user_order_item['active_status'] == 'awaiting' ? 'current' : 'hide' }}">
                                        <span>{{ labels('front_messages.awaiting', 'awaiting') }}</span>
                                    </div>
                                    <div
                                        class="step {{ $user_order_item['active_status'] == 'received' ? 'current' : '' }}">
                                        <span>{{ labels('front_messages.received', 'received') }}</span>
                                    </div>
                                    <div
                                        class="step {{ $user_order_item['active_status'] == 'processed' ? 'current' : '' }}">
                                        <span>{{ labels('front_messages.processed', 'processed') }}</span>
                                    </div>
                                    <div
                                        class="step {{ $user_order_item['active_status'] == 'shipped' ? 'current' : '' }}">
                                        <span>{{ labels('front_messages.shipped', 'shipped') }}</span>
                                    </div>
                                    <div
                                        class="step {{ $user_order_item['active_status'] == 'delivered' ? 'current' : '' }}">
                                        <span>{{ labels('front_messages.delivered', 'delivered') }}</span>
                                    </div>
                                @endif
                            </div>
                            <!-- End Tracking Steps -->
                            <!-- Order Table -->
                            <div class="table-bottom-brd table-responsive">
                                <table class="table align-middle text-center order-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ labels('front_messages.status', 'Status') }}</th>
                                            <th scope="col">{{ labels('front_messages.time', 'Time') }}</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user_order_item['status'] as $status)
                                            <tr>
                                                <td class="text-capitalize">{{ str_replace('_', ' ', $status[0]) }}
                                                </td>
                                                <td>{{ $status[1] }}</td>
                                                <td>
                                                    @if ($status[0] == 'shipped')
                                                        @php
                                                            $aftershipData = $user_order_item['aftership_data'];
                                                            $isValidJson = json_decode($aftershipData) !== null;
                                                        @endphp
                                                        <button class="btn btn-primary btn-sm" id="track_order"
                                                            data-item-id="{{ $user_order_item['id'] }}"
                                                            data-courier-agency-name="{{ $user_order_item['courier_agency_name'] }}"
                                                            data-order-id="{{ $user_order_item['order_id'] }}"
                                                            data-aftership-data="{{ $isValidJson ? $aftershipData : json_encode($aftershipData) }}">
                                                            {{ labels('front_messages.track_order', 'Track Order') }}
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endforeach

                    @if ($user_order['type'] != 'digital_product')

                        <div class="mb-2">
                            <div class="top-sec d-flex-justify-center justify-content-between mb-4">
                                <h2 class="mb-0">{{ labels('front_messages.address_details', 'Address Details') }}
                                </h2>
                            </div>
                            <div class="tracking-detail d-flex-center align-items-center mt-2">
                                <ul>
                                    <li>
                                        <div class="left"><span> {{ labels('front_messages.name', 'Name') }} </span>
                                        </div>
                                        <div class="right text-black">
                                            <span
                                                class="text-uppercase"><b>{{ $user_order['order_recipient_person'] }}</b></span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="left"><span> {{ labels('front_messages.address', 'Address') }}
                                            </span>
                                        </div>
                                        <div class="right"><span>{{ $user_order['address'] }}</span>
                                        </div>
                                    </li>
                                    @if ($user_order['otp'] != '0' || $user_order['otp'] != null)
                                        <li>
                                            <div class="left"><span> {{ labels('front_messages.otp', 'OTP') }}
                                                </span>
                                            </div>
                                            <div class="right"><span>{{ $user_order['otp'] }}</span>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="my-2">
                        <div class="top-sec d-flex-justify-center justify-content-between mb-4">
                            <h2 class="mb-0">{{ labels('front_messages.price_details', 'Price Details') }}</h2>
                        </div>
                        <div class="tracking-detail d-flex-center align-items-center mt-2">
                            <ul>
                                <li>
                                    <div class="left">
                                        <span>{{ labels('front_messages.payment_mode', 'Payment Mode') }}</span>
                                    </div>
                                    <div class="right">
                                        <span
                                            class="text-uppercase">{{ $user_order['wallet_balance'] == $user_order['final_total'] ? 'Wallet' : $user_order['payment_method'] }}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="left">
                                        <span>{{ labels('front_messages.total_price', 'total Price') }}</span>
                                    </div>
                                    <div class="right">
                                        <span>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['total'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                    </div>
                                </li>
                                <li class="{{ $user_order['delivery_charge'] == 0 ? 'd-none' : '' }}">
                                    <div class="left">
                                        <span>{{ labels('front_messages.delivary_charge', 'Delivary Charge') }}</span>
                                    </div>
                                    <div class="right">
                                        <span>+{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['delivery_charge'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                    </div>
                                </li>
                                @if (isset($transaction['fee']) && $transaction['fee'] != 0)
                                    <li>
                                        <div class="left">
                                            <span>{{ labels('front_messages.transaction_fee', 'Transaction Fee') }}</span>
                                        </div>
                                        <div class="right">
                                            <span>+{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $transaction['fee'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                        </div>
                                    </li>
                                @endif
                                <li class="{{ $user_order['promo_discount'] == 0 ? 'd-none' : '' }}">
                                    <div class="left">
                                        <span>{{ labels('front_messages.promo_discount', 'Coupon Discount') }}</span>
                                    </div>
                                    <div class="right">
                                        <span>-{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['promo_discount'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                    </div>
                                </li>
                                @if ($user_order['wallet_balance'] != 0.0)
                                    <li>
                                        <div class="left">
                                            <span>{{ labels('front_messages.wallet_balance_used', 'Wallet Balance Used') }}</span>
                                        </div>
                                        <div class="right">
                                            <span>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['wallet_balance'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                        </div>
                                    </li>
                                @endif
                                <li>
                                    <div class="left">
                                        <span>{{ labels('front_messages.final_total', 'Final Total') }}</span>
                                    </div>
                                    <div class="right text-black">
                                        @php
                                            if (isset($transaction['fee']) && $transaction['fee'] != 0) {
                                                $final_total = $user_order['final_total'] + $transaction['fee'];
                                            } else {
                                                $final_total = $user_order['final_total'];
                                            }
                                        @endphp
                                        <b>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $final_total * $currency_details[0]->exchange_rate, 2) : '' }}</b>
                                    </div>
                                </li>
                                {{-- <li>
                                    <div class="left">
                                        <span>{{ labels('front_messages.download_invoice', 'Download Invoice') }}</span>
                                    </div>
                                    <div class="right"><a
                                            href="{{ customUrl('/orders/generat_invoice_PDF/' . $user_orders['order_data'][0]->id) }}"
                                            target="_blank"
                                            class="link-primary text-underline">{{ labels('front_messages.downlaod', 'Downlaod') }}</a>
                                    </div>
                                </li> --}}
                            </ul>
                        </div>
                    </div>
                    <!-- End Order Table -->
                </div>
            </div>
        </div>
    </div>

    <!-- Track Order Modal -->
    <div class="modal fade" id="trackOrderModal" tabindex="-1" aria-labelledby="trackOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackOrderModalLabel">Order Tracking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderTrackingDetails">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Option Modal -->
    <div class="modal fade" id="refundOptionModal" tabindex="-1" aria-labelledby="refundOptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundOptionModalLabel">Choose Refund Method and Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="refundOptionForm">
                        <div class="mb-3">
                            <h6>Refund Method</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="refundMethod" id="refundWallet"
                                    value="wallet" checked>
                                <label class="form-check-label" for="refundWallet">
                                    Refund to Wallet
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="refundMethod" id="refundCard"
                                    value="card" @if ($transaction['transaction_type'] !== 'transaction' || $transaction['type'] !== 'stripe') disabled @endif>
                                <label class="form-check-label" for="refundCard">
                                    Refund to Card (via Stripe)<br>
                                    <small>Refunds take 5-10 days to appear on a customer's statement.</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>Select Items to Cancel</h6>
                            @foreach ($order_transaction as $user_order)
                                @foreach ($user_order['order_items'] as $user_order_item)
                                    @if ($user_order_item['is_cancelable'] == 1 && $user_order_item['is_already_cancelled'] == 0)
                                        <div class="form-check">
                                            <input class="form-check-input cancel-item-checkbox" type="checkbox"
                                                name="order_items[]" value="{{ $user_order_item['id'] }}"
                                                data-item-id="{{ $user_order_item['id'] }}"
                                                id="item_{{ $user_order_item['id'] }}">
                                            <label class="form-check-label" for="item_{{ $user_order_item['id'] }}">
                                                {{ $user_order_item['product_name'] }}
                                                {{ $user_order_item['variant_name'] ? ' - ' . $user_order_item['variant_name'] : '' }}
                                                ({{ $currency_symbol . number_format((float) $user_order_item['sub_total'], 2) }})
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                        <input type="hidden" id="paymentMethod" value="{{ $transaction['transaction_type'] }}">
                        <input type="hidden" id="paymentType" value="{{ $transaction['type'] }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmRefund">Confirm Refund</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).on("click", "#track_order", function(e) {
                e.preventDefault();

                const orderTrackingDetails = document.getElementById("orderTrackingDetails");
                const orderId = $(this).data("order-id");
                const courierAgencyName = $(this).data("courier-agency-name");
                const trackingData = $(this).data("aftership-data");
                let aftershipData;

                orderTrackingDetails.innerHTML = '';
                const $modal = $('#trackOrderModal');
                $modal.modal('show');

                try {
                    aftershipData = typeof trackingData === 'string' ? JSON.parse(trackingData) : trackingData;
                } catch (error) {
                    orderTrackingDetails.innerHTML = `
            <div class="alert alert-danger" role="alert">
                Parsing error: ${error.message}<br>
                Raw data: ${trackingData}
            </div>`;
                    console.error("Invalid JSON:", trackingData);
                    return;
                }

                if (!aftershipData || Object.keys(aftershipData).length === 0) {
                    orderTrackingDetails.innerHTML = `
            <div class="alert alert-warning" role="alert">
                No tracking information available.
            </div>`;
                    return;
                }

                const formatLocalDate = (dateString) => {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return isNaN(date) ? 'Invalid date' : date.toLocaleString('en-US');
                };

                const renderCheckpoints = (checkpoints) => {
                    if (!checkpoints || !Array.isArray(checkpoints) || checkpoints.length === 0) {
                        return '<div class="alert alert-info mt-3" role="alert">No checkpoints available.</div>';
                    }

                    const checkpointGroups = {
                        Delivered: [],
                        InTransit: [],
                        InfoReceived: [],
                        Other: []
                    };

                    checkpoints.forEach(cp => {
                        if (checkpointGroups[cp.tag]) checkpointGroups[cp.tag].push(cp);
                        else checkpointGroups.Other.push(cp);
                    });

                    let html = '';
                    const checkpointTypes = ['Delivered', 'InTransit', 'InfoReceived', 'Other'];

                    checkpointTypes.forEach(type => {
                        const group = checkpointGroups[type];
                        if (group.length === 0) return;

                        if (type === 'InTransit' && group.length > 1) {
                            const latest = group[group.length - 1];
                            html += `
                    <div class="mb-3">
                        <h6 class="text-warning mb-1">${formatLocalDate(latest.checkpoint_time)} - ${latest.tag}</h6>
                        <div class="fs-6"><strong>Message:</strong> ${latest.message || 'N/A'}</div>
                        <div class="fs-6"><strong>Location:</strong> ${latest.location || 'N/A'}</div>
                        <div class="fs-6"><strong>Subtag:</strong> ${latest.subtag_message || 'N/A'}</div>
                    </div>
                    <div class="collapse mb-0" id="allInTransit">
                        ${group.slice(0, -1).reverse().map(cp => `
                                                                                                                <div class="mb-3">
                                                                                                                    <h6 class="text-warning mb-1">${formatLocalDate(cp.checkpoint_time)} - ${cp.tag}</h6>
                                                                                                                    <div class="fs-6"><strong>Message:</strong> ${cp.message || 'N/A'}</div>
                                                                                                                    <div class="fs-6"><strong>Location:</strong> ${cp.location || 'N/A'}</div>
                                                                                                                    <div class="fs-6"><strong>Subtag:</strong> ${cp.subtag_message || 'N/A'}</div>
                                                                                                                </div>
                                                                                                            `).join('')}
                    </div>
                    <p class="mb-3 mt-0">
                        <a href="#" class="text-primary fs-6" data-bs-target="#allInTransit"
                            aria-expanded="false" aria-controls="allInTransit" id="toggleInTransit">
                            Show all updates
                        </a>
                    </p>`;
                        } else {
                            html += group.reverse().map(cp => `
                    <div class="mb-3">
                        <h6 class="text-${type === 'Delivered' ? 'success' : type === 'InfoReceived' ? 'info' : 'warning'} mb-1">
                            ${formatLocalDate(cp.checkpoint_time)} - ${cp.tag}
                        </h6>
                        <div class="fs-6"><strong>Message:</strong> ${cp.message || 'N/A'}</div>
                        <div class="fs-6"><strong>Location:</strong> ${cp.location || 'N/A'}</div>
                        <div class="fs-6"><strong>Subtag:</strong> ${cp.subtag_message || 'N/A'}</div>
                    </div>
                `).join('');
                        }
                    });

                    return html;
                };

                const html = `
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tracking Details (Order ID: ${orderId})</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">General Information</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Courier Service:</span>
                                <span class="fw-bold">${courierAgencyName || 'N/A'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Tracking ID:</span>
                                <span class="fw-bold">${aftershipData.tracking_number || 'N/A'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Tracking Link:</span>
                                <span>
                                    ${aftershipData.courier_tracking_link
                                        ? `<a href="${aftershipData.courier_tracking_link}" target="_blank" class="text-primary">View</a>`
                                        : 'N/A'}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Status:</span>
                                <span class="badge bg-${aftershipData.tag === 'Delivered' ? 'success' : aftershipData.tag === 'InTransit' ? 'warning' : 'info'}">
                                    ${aftershipData.tag || 'Unknown'}
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Delivery Information</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Origin:</span>
                                <span>${aftershipData.origin_city || 'N/A'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Destination:</span>
                                <span>${aftershipData.destination_city || 'N/A'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Shipment Date:</span>
                                <span>${formatLocalDate(aftershipData.shipment_pickup_date)}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Delivery Date:</span>
                                <span>${formatLocalDate(aftershipData.shipment_delivery_date)}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <h6 class="mt-4 border-bottom pb-2">Tracking Checkpoints</h6>
                ${renderCheckpoints(aftershipData.checkpoints)}
            </div>
        </div>
    `;

                orderTrackingDetails.innerHTML = html;

                // Оновлений обробник для #toggleInTransit
                const toggleLink = document.getElementById('toggleInTransit');
                if (toggleLink) {
                    const collapseElement = document.getElementById('allInTransit');

                    // Переконуємося, що блок спочатку згорнутий
                    collapseElement.classList.remove('show');

                    // Ініціалізація Bootstrap Collapse
                    const collapseInstance = new bootstrap.Collapse(collapseElement, {
                        toggle: false // Не перемикаємо автоматично при ініціалізації
                    });

                    // Видаляємо попередні обробники, якщо вони є
                    toggleLink.removeEventListener('click', toggleLink._clickHandler);
                    toggleLink._clickHandler = (e) => {
                        e.preventDefault();
                        if (collapseElement.classList.contains('show')) {
                            collapseInstance.hide();
                            toggleLink.textContent = 'Show all updates';
                        } else {
                            collapseInstance.show();
                            toggleLink.textContent = 'Hide all updates';
                        }
                    };
                    toggleLink.addEventListener('click', toggleLink._clickHandler);

                }
            });
        </script>
    @endpush
