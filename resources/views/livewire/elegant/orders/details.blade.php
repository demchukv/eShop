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
                                                data-item-id="{{ $user_order_item['id'] }}">{{ labels('front_messages.cancle', 'Cancle') }}</button>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user_order_item['status'] as $status)
                                            <tr>
                                                <td class="text-capitalize">{{ str_replace('_', ' ', $status[0]) }}
                                                </td>
                                                <td>{{ $status[1] }}</td>
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
                                        <b>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['final_total'] * $currency_details[0]->exchange_rate, 2) : '' }}</b>
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
</div>
