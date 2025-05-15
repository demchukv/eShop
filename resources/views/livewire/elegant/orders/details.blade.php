<?php
$bread_crumb['page_main_bread_crumb'] = labels('front_messages.order_details', 'Order Details');
?>
{{-- @dd($order_transaction); --}}
<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo />
    <div class="container-fluid h-100">
        <div class="row">
            <x-utility.my_account_slider.account_slider :$user_info />
            <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                <div class="orders-card mt-0 h-100">
                    <div class="top-sec d-flex-justify-center justify-content-between mb-4">
                        <h2 class="mb-0">{{ labels('front_messages.orders_details', 'Orders Details') }}</h2>
                        <h3>{{ labels('front_messages.order_id', 'order ID') }}:
                            #{{ $user_orders['order_data'][0]->id }}
                        </h3>

                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-12">

                            @php
                                $currency_symbol = $user_orders['order_data'][0]->order_payment_currency_code;
                                $currency_details = fetchDetails('currencies', [
                                    'symbol' => $currency_symbol,
                                ]);
                            @endphp
                            @foreach ($order_transaction as $user_order)
                                @foreach ($user_order['order_items'] as $user_order_item)
                                    <div class="row mt-3 order-item-info">
                                        <div class="col-lg-2 col-md-3 col-sm-4 order-item-photo">
                                            @php
                                                $order_image = dynamic_image($user_order_item['image_sm'], 230);
                                            @endphp
                                            <a wire:navigate
                                                href="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}"
                                                class=""
                                                data-link="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}">
                                                <img class="blur-up lazyload" data-src="{{ $order_image }}"
                                                    src="{{ $order_image }}"
                                                    alt="{{ $user_order_item['product_name'] }}"
                                                    title="{{ $user_order_item['product_name'] }}">
                                            </a>

                                        </div>
                                        <div class="col-lg-6 col-md-9 col-sm-8 order-item-data">
                                            <div class="order-item-data-left">
                                                <div class="tracking-detail">
                                                    <p class="m-0 p-0"><a wire:navigate
                                                            href="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}"
                                                            class="order-item-data-product-name"
                                                            data-link="{{ customUrl($user_order['order_items'][0]['order_type'] == 'regular_order' ? 'products/' . $user_order['order_items'][0]['slug'] : 'combo-products/' . $user_order['order_items'][0]['slug']) }}">
                                                            <span>{{ $user_order_item['product_name'] . ($user_order_item['variant_name'] ? ' - ' . $user_order_item['variant_name'] : '') }}</span>

                                                        </a></p>
                                                    @if ($user_order_item['is_cancelable'] == 1)
                                                        <p class="m-0 pb-1 order-item-data-cancellable">
                                                            {{ labels('front_messages.cancelable_till', 'Cancelable Till') }}
                                                            {{ $user_order_item['cancelable_till'] }}
                                                        </p>
                                                    @endif

                                                    <p class="mt-2 mb-0 pb-1 order-item-data-total-price">
                                                        {{ labels('front_messages.total_price', 'Total Price') }}:
                                                        {{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order_item['price'] * $currency_details[0]->exchange_rate, 2) : '' }}
                                                    </p>
                                                    <p class="m-0 pb-1 order-item-data-final-total">
                                                        {{ labels('front_messages.final_total', 'Final Total') }}:
                                                        <b>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order_item['sub_total'] * $currency_details[0]->exchange_rate, 2) : '' }}</b>
                                                    </p>
                                                    <div class="order-item-data-seller">
                                                        <span class="order-item-data-seller-icon"><svg width="19"
                                                                height="19" viewBox="0 0 19 19" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M19 7.70837C19 7.6521 18.9938 7.596 18.9815 7.5411L17.9794 3.01787C17.7932 2.15934 17.3175 1.39094 16.6321 0.841489C15.9466 0.292041 15.0931 -0.00503844 14.2147 6.46641e-05H5.28533C4.4074 -0.0043293 3.55461 0.293074 2.8698 0.842464C2.18499 1.39185 1.70974 2.15987 1.52367 3.01787L0.518501 7.5411C0.506174 7.596 0.499969 7.6521 0.500001 7.70837V8.4792C0.499379 9.22968 0.773586 9.9544 1.27083 10.5165V14.6458C1.27206 15.6677 1.67851 16.6473 2.40105 17.3698C3.12358 18.0923 4.10319 18.4988 5.125 18.5H14.375C15.3968 18.4988 16.3764 18.0923 17.099 17.3698C17.8215 16.6473 18.2279 15.6677 18.2292 14.6458V10.5165C18.7264 9.9544 19.0006 9.22968 19 8.4792V7.70837ZM2.04167 7.79239L3.02833 3.35241C3.14014 2.83769 3.42533 2.37698 3.83618 2.04737C4.24703 1.71777 4.75862 1.53926 5.28533 1.54173H5.89583V3.85422C5.89583 4.05865 5.97705 4.25472 6.12161 4.39928C6.26617 4.54384 6.46223 4.62505 6.66667 4.62505C6.8711 4.62505 7.06717 4.54384 7.21173 4.39928C7.35629 4.25472 7.4375 4.05865 7.4375 3.85422V1.54173H12.0625V3.85422C12.0625 4.05865 12.1437 4.25472 12.2883 4.39928C12.4328 4.54384 12.6289 4.62505 12.8333 4.62505C13.0378 4.62505 13.2338 4.54384 13.3784 4.39928C13.523 4.25472 13.6042 4.05865 13.6042 3.85422V1.54173H14.2147C14.7414 1.53926 15.253 1.71777 15.6638 2.04737C16.0747 2.37698 16.3599 2.83769 16.4717 3.35241L17.4583 7.79239V8.4792C17.4583 8.88808 17.2959 9.2802 17.0068 9.56932C16.7177 9.85844 16.3255 10.0209 15.9167 10.0209H15.1458C14.737 10.0209 14.3448 9.85844 14.0557 9.56932C13.7666 9.2802 13.6042 8.88808 13.6042 8.4792C13.6042 8.27477 13.523 8.0787 13.3784 7.93414C13.2338 7.78958 13.0378 7.70837 12.8333 7.70837C12.6289 7.70837 12.4328 7.78958 12.2883 7.93414C12.1437 8.0787 12.0625 8.27477 12.0625 8.4792C12.0625 8.88808 11.9001 9.2802 11.611 9.56932C11.3218 9.85844 10.9297 10.0209 10.5208 10.0209H8.97917C8.57029 10.0209 8.17816 9.85844 7.88904 9.56932C7.59993 9.2802 7.4375 8.88808 7.4375 8.4792C7.4375 8.27477 7.35629 8.0787 7.21173 7.93414C7.06717 7.78958 6.8711 7.70837 6.66667 7.70837C6.46223 7.70837 6.26617 7.78958 6.12161 7.93414C5.97705 8.0787 5.89583 8.27477 5.89583 8.4792C5.89583 8.88808 5.73341 9.2802 5.44429 9.56932C5.15517 9.85844 4.76304 10.0209 4.35417 10.0209H3.58333C3.17446 10.0209 2.78233 9.85844 2.49321 9.56932C2.20409 9.2802 2.04167 8.88808 2.04167 8.4792V7.79239ZM14.375 16.9583H5.125C4.51169 16.9583 3.92349 16.7147 3.48982 16.281C3.05614 15.8473 2.8125 15.2592 2.8125 14.6458V11.4646C3.06425 11.5298 3.32328 11.5627 3.58333 11.5625H4.35417C4.79186 11.5628 5.22456 11.4696 5.62335 11.2892C6.02214 11.1088 6.37783 10.8454 6.66667 10.5165C6.9555 10.8454 7.3112 11.1088 7.70999 11.2892C8.10878 11.4696 8.54148 11.5628 8.97917 11.5625H10.5208C10.9585 11.5628 11.3912 11.4696 11.79 11.2892C12.1888 11.1088 12.5445 10.8454 12.8333 10.5165C13.1222 10.8454 13.4779 11.1088 13.8767 11.2892C14.2754 11.4696 14.7081 11.5628 15.1458 11.5625H15.9167C16.1767 11.5627 16.4357 11.5298 16.6875 11.4646V14.6458C16.6875 15.2592 16.4439 15.8473 16.0102 16.281C15.5765 16.7147 14.9883 16.9583 14.375 16.9583Z"
                                                                    fill="#2A3029" />
                                                            </svg></span>
                                                        <a wire:navigate
                                                            href="{{ customUrl('sellers/' . $user_order_item['store_slug']) }}"
                                                            class="order-item-data-seller-name"
                                                            data-link="{{ customUrl('sellers/' . $user_order_item['store_slug']) }}">
                                                            <span>{{ $user_order_item['store_name'] }}</span>

                                                        </a>
                                                    </div>
                                                    <span class="order-item-chat"
                                                        title="Chat with seller"><x-chat-button
                                                            :userId="$user_order_item['main_seller_id']" /></span>
                                                </div>
                                            </div>
                                            <div class="order-item-data-buttons">
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
                                                    $max_days_to_return_item =
                                                        $system_settings['max_days_to_return_item'] ?? 0;

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
                                                @if (
                                                    $user_order_item['is_already_cancelled'] == 0 &&
                                                        $user_order_item['is_cancelable'] == 1 &&
                                                        $cancellable_index >= $active_index)
                                                    <button class="btn btn-secondary btn-sm update_order_item_status"
                                                        data-status="cancelled"
                                                        data-item-id="{{ $user_order_item['id'] }}">{{ labels('front_messages.cancle', 'Cancel') }}</button>
                                                @endif
                                                @if (
                                                    ($user_order_item['active_status'] == 'shipped' || $user_order_item['active_status'] == 'delivered') &&
                                                        $user_order_item['is_completed'] == 0)
                                                    <button class="btn btn-secondary btn-sm confirm_received_btn"
                                                        data-item-id="{{ $user_order_item['id'] }}">{{ labels('front_messages.confirm_received', 'Confirm received') }}</button>
                                                    @if ($user_order_item['active_status'] == 'delivered')
                                                        <div class="fs-8">
                                                            {{ labels('front_messages.confirm_info', 'The system will automatically confirm receipt after 81 days. If the item you received is defective or not as described, you can open a dispute within 15 days of receipt.') }}
                                                        </div>
                                                    @endif
                                                @endif
                                                @if (
                                                    $user_order_item['is_returnable'] == 1 &&
                                                        $user_order_item['return_request_submitted'] != 1 &&
                                                        $user_order_item['active_status'] == 'delivered' &&
                                                        $is_return_time_is_over == true &&
                                                        $user_order_item['is_completed'] == 1)
                                                    @if ($user_order_item['is_write_review'] == 0)
                                                        <a class="btn btn-secondary btn-sm review_order_item"
                                                            href="{{ route('front_end.orders.review', $user_order_item['id']) }}"
                                                            data-item-id="{{ $user_order_item['id'] }}">
                                                            {{ labels('front_messages.write_review', 'Write Review') }}
                                                        </a>
                                                    @endif
                                                    @if ($user_orders['order_data'][0]->is_custom_courier == 1)
                                                        <a class="btn btn-secondary btn-sm review_order_item"
                                                            href="{{ route('orders.return-options', $user_order_item['id']) }}">
                                                            {{ labels('front_messages.returns_refunds', 'Returns/Refunds') }}
                                                        </a>
                                                    @else
                                                        <button
                                                            class="btn btn-secondary btn-sm update_order_item_status"
                                                            data-status="returned"
                                                            data-item-id="{{ $user_order_item['id'] }}">{{ labels('front_messages.return', 'Return') }}</button>
                                                    @endif
                                                @endif
                                                @if ($user_order_item['active_status'] == 'return_request_pending' && !empty($user_order_item['disput_id']))
                                                    <a class="btn btn-secondary btn-sm review_order_item"
                                                        href="{{ route('disputs.show', $user_order_item['disput_id']) }}"
                                                        data-item-id="{{ $user_order_item['id'] }}">
                                                        {{ labels('front_messages.dispu_show', 'Disput Show') }}
                                                    </a>
                                                @endif
                                                @if ($user_order_item['active_status'] == 'shipped')
                                                    @php
                                                        $aftershipData = $user_order_item['aftership_data'];
                                                        $isValidJson = json_decode($aftershipData) !== null;
                                                    @endphp
                                                    <button class="btn btn-secondary btn-sm" id="track_order"
                                                        data-item-id="{{ $user_order_item['id'] }}"
                                                        data-courier-agency-name="{{ $user_order_item['courier_agency_name'] }}"
                                                        data-order-id="{{ $user_order_item['order_id'] }}"
                                                        data-aftership-data="{{ $isValidJson ? $aftershipData : json_encode($aftershipData) }}">
                                                        {{ labels('front_messages.track_order', 'Track Order') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Status Order -->
                                    <!-- Tracking Steps -->
                                    @php
                                        if ($user_order_item['active_status'] == 'cancelled') {
                                            $totalSteps = 2;
                                            $currentWidth = 100;
                                            $statusDate = 100;
                                        } elseif (
                                            $user_order_item['is_already_returned'] == 1 ||
                                            $user_order_item['return_request_submitted'] >= 1 ||
                                            $user_order_item['active_status'] == 'return_request_pending'
                                        ) {
                                            $totalStaeps = 2;
                                            $currentWidth = 100;
                                            $statusDate = 100;
                                        } else {
                                            $totalSteps = $user_order_item['active_status'] == 'awaiting' ? 5 : 4;
                                            if ($user_order_item['active_status'] == 'awaiting') {
                                                $currentStep = 1;
                                            }
                                            if ($user_order_item['active_status'] == 'received') {
                                                $currentStep = 1;
                                            }
                                            if ($user_order_item['active_status'] == 'processed') {
                                                $currentStep = 2;
                                            }
                                            if ($user_order_item['active_status'] == 'shipped') {
                                                $currentStep = 3;
                                            }
                                            if ($user_order_item['active_status'] == 'delivered') {
                                                $currentStep = 4;
                                            }
                                            $halfLine = ($totalSteps - 2) * 2 + 2; // half line between two points
                                            $halfLineWidth = 100 / $halfLine;
                                            if ($currentStep == 1) {
                                                $currentWidth = $halfLineWidth;
                                            } else {
                                                $currentWidth = (($currentStep - 1) * 2 + 1) * $halfLineWidth;
                                            }
                                            if ($currentStep == $totalSteps) {
                                                $currentWidth = 100;
                                            }
                                            $statusDate = 100 / ($totalSteps - 1);
                                        }
                                    @endphp
                                    <div class="tracking-step-container mt-5 mb-5">
                                        <div class="tracking-line">
                                            <div class="tracking-line-ready" style="width:{{ $currentWidth }}%;"></div>
                                        </div>
                                        @if ($user_order_item['active_status'] == 'cancelled')
                                            @foreach ($user_order_item['status'] as $status)
                                                <div class="tracking-step current">
                                                    <span class="tracking-step-icon-container">
                                                        @if ($status[0] == 'received')
                                                            <svg width="22" height="26" viewBox="0 0 22 26"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M2.39475 18.1644V0.65625H1.36485H0.334962L0.334962 18.1644C0.334962 19.8679 1.7212 21.2541 3.42464 21.2541V21.769C3.42464 23.7567 5.04157 25.3737 7.02926 25.3737C9.01696 25.3737 10.6339 23.7567 10.6339 21.769V21.2541H13.7236V21.769C13.7236 23.7567 15.3405 25.3737 17.3282 25.3737C19.3159 25.3737 20.9328 23.7567 20.9328 21.769V21.2541H21.9627V19.1943H3.42464C2.85717 19.1943 2.39475 18.7329 2.39475 18.1644ZM8.5741 21.769C8.5741 22.6208 7.88098 23.3139 7.02926 23.3139C6.17754 23.3139 5.48442 22.6208 5.48442 21.769V21.2541H8.5741V21.769ZM18.873 21.769C18.873 22.6208 18.1799 23.3139 17.3282 23.3139C16.4765 23.3139 15.7833 22.6208 15.7833 21.769V21.2541H18.873V21.769ZM4.45453 17.1345H21.9627V9.92528H4.45453V17.1345ZM6.51432 11.9851H19.9029V15.0747H6.51432V11.9851ZM11.6638 0.65625H4.45453V7.8655H11.6638V0.65625ZM9.604 5.80571H6.51432V2.71603H9.604V5.80571ZM13.7236 1.68614V7.8655H21.9627V1.68614H13.7236ZM19.9029 5.80571H15.7833V3.74593H19.9029V5.80571Z"
                                                                    fill="white" />
                                                            </svg>
                                                        @else
                                                        @endif
                                                    </span>
                                                    <span class="tracking-label">{{ $status[0] }}</span>
                                                </div>
                                            @endforeach
                                        @elseif (
                                            $user_order_item['is_already_returned'] == 1 ||
                                                $user_order_item['return_request_submitted'] >= 1 ||
                                                $user_order_item['active_status'] == 'return_request_pending')
                                            @foreach ($user_order_item['status'] as $status)
                                                <div
                                                    class="tracking-step {{ $status[0] == $user_order_item['active_status'] ? 'current' : '' }}">
                                                    <span>{{ str_replace('_', ' ', $status[0]) }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            @php
                                                $stepBlocks = $user_order_item['active_status'] == 'awaiting' ? 5 : 4;
                                                $stepBlockWidt = 100 / $stepBlocks;
                                            @endphp
                                            <div
                                                class="tracking-step {{ $user_order_item['active_status'] == 'awaiting' || array_key_exists('awaiting', $user_order_item['status_name']) ? 'current' : 'hide' }}">
                                                <span class="tracking-step-icon-container"></span>
                                                <span
                                                    class="tracking-label">{{ labels('front_messages.awaiting', 'Awaiting') }}</span>
                                            </div>
                                            <div
                                                class="tracking-step {{ $user_order_item['active_status'] == 'received' || array_key_exists('received', $user_order_item['status_name']) ? 'current' : '' }}">
                                                <span class="tracking-step-icon-container"><svg width="22"
                                                        height="26" viewBox="0 0 22 26" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.39475 18.1644V0.65625H1.36485H0.334962L0.334962 18.1644C0.334962 19.8679 1.7212 21.2541 3.42464 21.2541V21.769C3.42464 23.7567 5.04157 25.3737 7.02926 25.3737C9.01696 25.3737 10.6339 23.7567 10.6339 21.769V21.2541H13.7236V21.769C13.7236 23.7567 15.3405 25.3737 17.3282 25.3737C19.3159 25.3737 20.9328 23.7567 20.9328 21.769V21.2541H21.9627V19.1943H3.42464C2.85717 19.1943 2.39475 18.7329 2.39475 18.1644ZM8.5741 21.769C8.5741 22.6208 7.88098 23.3139 7.02926 23.3139C6.17754 23.3139 5.48442 22.6208 5.48442 21.769V21.2541H8.5741V21.769ZM18.873 21.769C18.873 22.6208 18.1799 23.3139 17.3282 23.3139C16.4765 23.3139 15.7833 22.6208 15.7833 21.769V21.2541H18.873V21.769ZM4.45453 17.1345H21.9627V9.92528H4.45453V17.1345ZM6.51432 11.9851H19.9029V15.0747H6.51432V11.9851ZM11.6638 0.65625H4.45453V7.8655H11.6638V0.65625ZM9.604 5.80571H6.51432V2.71603H9.604V5.80571ZM13.7236 1.68614V7.8655H21.9627V1.68614H13.7236ZM19.9029 5.80571H15.7833V3.74593H19.9029V5.80571Z"
                                                            fill="white" />
                                                    </svg></span>
                                                <span
                                                    class="tracking-label">{{ labels('front_messages.received', 'Received') }}</span>
                                            </div>
                                            <div
                                                class="tracking-step {{ $user_order_item['active_status'] == 'processed' || array_key_exists('processed', $user_order_item['status_name']) ? 'current' : '' }}">
                                                <span class="tracking-step-icon-container"><svg width="26"
                                                        height="26" viewBox="0 0 26 26" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.59299 13.1683C2.59299 13.7368 2.1316 14.1982 1.5631 14.1982C0.994595 14.1982 0.533203 13.7368 0.533203 13.1683C0.533203 6.35348 6.07711 0.80957 12.8919 0.80957C15.9682 0.80957 18.8776 1.95584 21.1311 3.97855V1.83946C21.1311 1.27096 21.5914 0.80957 22.1609 0.80957C22.7305 0.80957 23.1908 1.27096 23.1908 1.83946V5.44409C23.1908 6.86328 22.0363 8.01882 20.6161 8.01882H17.0115C16.442 8.01882 15.9816 7.55743 15.9816 6.98892C15.9816 6.42042 16.442 5.95903 17.0115 5.95903H20.2309C18.2999 3.99091 15.6757 2.86936 12.8919 2.86936C7.21309 2.86936 2.59299 7.48945 2.59299 13.1683ZM24.2207 12.1384C23.6512 12.1384 23.1908 12.5998 23.1908 13.1683C23.1908 18.8471 18.5707 23.4672 12.8919 23.4672C10.1081 23.4672 7.48395 22.3457 5.55187 20.3775H8.77234C9.34084 20.3775 9.80223 19.9161 9.80223 19.3476C9.80223 18.7791 9.34084 18.3177 8.77234 18.3177H5.16772C3.74853 18.3177 2.59299 19.4733 2.59299 20.8925V24.4971C2.59299 25.0656 3.05438 25.527 3.62288 25.527C4.19138 25.527 4.65277 25.0656 4.65277 24.4971V22.358C6.90515 24.3807 9.81562 25.527 12.8919 25.527C19.7067 25.527 25.2506 19.9831 25.2506 13.1683C25.2506 12.5998 24.7903 12.1384 24.2207 12.1384ZM18.7582 10.9705L17.7551 11.5493C17.926 12.0611 18.0414 12.5987 18.0414 13.1683C18.0414 13.7378 17.9271 14.2754 17.7551 14.7873L18.7582 15.3661C19.2515 15.6503 19.4204 16.2806 19.1351 16.7729C18.9436 17.1035 18.5985 17.2878 18.2422 17.2878C18.0671 17.2878 17.89 17.2436 17.7283 17.1498L16.7262 16.571C16.0001 17.3888 15.0331 17.9871 13.9218 18.2127V19.3466C13.9218 19.9151 13.4604 20.3765 12.8919 20.3765C12.3234 20.3765 11.862 19.9151 11.862 19.3466V18.2127C10.7508 17.9861 9.7837 17.3877 9.05762 16.571L8.05554 17.1498C7.89281 17.2436 7.71567 17.2878 7.54162 17.2878C7.18528 17.2878 6.83923 17.1035 6.6487 16.7729C6.36445 16.2796 6.53336 15.6503 7.02564 15.3661L8.02876 14.7873C7.8578 14.2754 7.74245 13.7378 7.74245 13.1683C7.74245 12.5987 7.85677 12.0611 8.02876 11.5493L7.02564 10.9705C6.53336 10.6862 6.36445 10.0559 6.6487 9.56366C6.93295 9.07034 7.56119 8.90143 8.05554 9.18672L9.05762 9.76552C9.7837 8.94778 10.7508 8.34941 11.862 8.12387V6.98995C11.862 6.42145 12.3234 5.96006 12.8919 5.96006C13.4604 5.96006 13.9218 6.42145 13.9218 6.98995V8.12387C15.0331 8.35044 16.0001 8.94881 16.7262 9.76552L17.7283 9.18672C18.2195 8.90143 18.8498 9.07034 19.1351 9.56366C19.4194 10.057 19.2505 10.6862 18.7582 10.9705ZM15.9816 13.1683C15.9816 11.4648 14.5954 10.0786 12.8919 10.0786C11.1885 10.0786 9.80223 11.4648 9.80223 13.1683C9.80223 14.8717 11.1885 16.258 12.8919 16.258C14.5954 16.258 15.9816 14.8717 15.9816 13.1683Z"
                                                            fill="white" />
                                                    </svg></span>
                                                <span
                                                    class="tracking-label">{{ labels('front_messages.processed', 'Processed') }}</span>
                                            </div>
                                            <div
                                                class="tracking-step {{ $user_order_item['active_status'] == 'shipped' || array_key_exists('shipped', $user_order_item['status_name']) ? 'current' : '' }}">
                                                <span class="tracking-step-icon-container"><svg width="28"
                                                        height="26" viewBox="0 0 28 26" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M21.9084 5.30365H19.5845C19.2085 3.16559 17.6231 1.40054 15.4737 0.845523C14.8818 0.691601 14.2664 1.05337 14.1108 1.65333C13.9564 2.25442 14.3178 2.86673 14.9165 3.02066C16.3979 3.40378 17.4328 4.74638 17.4328 6.2856V18.7859H5.125C3.89085 18.7859 2.88721 17.7781 2.88721 16.5388V12.0448H5.68444C6.30319 12.0448 6.80334 11.5414 6.80334 10.9212C6.80334 10.3011 6.30319 9.79772 5.68444 9.79772H2.88721C1.65306 9.79772 0.649414 10.8055 0.649414 12.0448V16.5388C0.649414 18.6511 2.10845 20.4273 4.06764 20.906C4.02736 21.1318 4.0061 21.3621 4.0061 21.5947C4.0061 23.7631 5.76277 25.527 7.92224 25.527C10.0817 25.527 11.8384 23.7631 11.8384 21.5947C11.8384 21.4048 11.8238 21.2183 11.797 21.0329H16.3554C16.3285 21.2183 16.314 21.4048 16.314 21.5947C16.314 23.7631 18.0706 25.527 20.2301 25.527C22.3896 25.527 24.1462 23.7631 24.1462 21.5947C24.1462 21.3621 24.125 21.1318 24.0847 20.906C26.0439 20.4273 27.5029 18.6511 27.5029 16.5388V10.9212C27.5029 7.8237 24.9932 5.30365 21.9084 5.30365ZM25.2651 10.9212V12.0448H19.6706V7.55068H21.9084C23.7591 7.55068 25.2651 9.06294 25.2651 10.9212ZM9.60058 21.5947C9.60058 22.5238 8.84756 23.28 7.92224 23.28C6.99691 23.28 6.24389 22.5238 6.24389 21.5947C6.24389 21.3823 6.28529 21.1947 6.34571 21.0329H9.49988C9.5603 21.1947 9.6017 21.3823 9.6017 21.5947H9.60058ZM20.2301 23.28C19.3048 23.28 18.5517 22.5238 18.5517 21.5947C18.5517 21.3823 18.5931 21.1947 18.6536 21.0329H21.8077C21.8681 21.1947 21.9095 21.3823 21.9095 21.5947C21.9095 22.5238 21.1554 23.28 20.2301 23.28ZM23.0273 18.7859H19.6706V14.2918H25.2651V16.5388C25.2651 17.7781 24.2615 18.7859 23.0273 18.7859ZM0.649414 1.93309C0.649414 1.31291 1.14956 0.80957 1.76831 0.80957H10.8918C11.5105 0.80957 12.0107 1.31291 12.0107 1.93309C12.0107 2.55327 11.5105 3.05661 10.8918 3.05661H1.76831C1.14956 3.05661 0.649414 2.55327 0.649414 1.93309ZM0.649414 6.42717C0.649414 5.80698 1.14956 5.30365 1.76831 5.30365H8.65399C9.27274 5.30365 9.77289 5.80698 9.77289 6.42717C9.77289 7.04735 9.27274 7.55068 8.65399 7.55068H1.76831C1.14956 7.55068 0.649414 7.04735 0.649414 6.42717Z"
                                                            fill="white" />
                                                    </svg></span>
                                                <span
                                                    class="tracking-label">{{ labels('front_messages.shipped', 'Shipped') }}</span>
                                            </div>
                                            <div
                                                class="tracking-step {{ $user_order_item['active_status'] == 'delivered' || array_key_exists('delivered', $user_order_item['status_name']) ? 'current' : '' }}">
                                                <span class="tracking-step-icon-container"><svg width="26"
                                                        height="26" viewBox="0 0 26 26" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M22.2234 18.3177H21.1936V17.2878C21.1936 16.7193 20.7332 16.258 20.1637 16.258C19.5941 16.258 19.1338 16.7193 19.1338 17.2878V18.3177H13.9843C12.2809 18.3177 10.8946 19.704 10.8946 21.4074V21.9224C10.8946 22.7741 10.2015 23.4672 9.34979 23.4672C8.49807 23.4672 7.80495 22.7741 7.80495 21.9224V4.41419C7.80495 3.85908 7.66797 3.33899 7.44346 2.86936H9.86473C10.4343 2.86936 10.8946 2.40796 10.8946 1.83946C10.8946 1.27096 10.4343 0.80957 9.86473 0.80957H4.20033C2.21263 0.80957 0.595703 2.4265 0.595703 4.41419V4.92914C0.595703 6.63258 1.98194 8.01882 3.68538 8.01882H5.74517V21.9224C5.74517 23.909 7.36107 25.526 9.34773 25.527H21.7085C23.6962 25.527 25.3131 23.9101 25.3131 21.9224V21.4074C25.3131 19.704 23.9269 18.3177 22.2234 18.3177ZM5.74517 5.95903H3.68538C3.11688 5.95903 2.65549 5.49661 2.65549 4.92914V4.41419C2.65549 3.56247 3.34861 2.86936 4.20033 2.86936C5.05205 2.86936 5.74517 3.56247 5.74517 4.41419V5.95903ZM23.2533 21.9224C23.2533 22.7741 22.5602 23.4672 21.7085 23.4672H12.6063C12.8298 22.9986 12.9544 22.4754 12.9544 21.9224V21.4074C12.9544 20.8399 13.4158 20.3775 13.9843 20.3775H22.2234C22.7919 20.3775 23.2533 20.8399 23.2533 21.4074V21.9224ZM18.1039 15.2281C22.0782 15.2281 25.3131 11.9942 25.3131 8.01882C25.3131 4.04343 22.0782 0.80957 18.1039 0.80957C14.1295 0.80957 10.8946 4.04343 10.8946 8.01882C10.8946 11.9942 14.1295 15.2281 18.1039 15.2281ZM18.1039 2.86936C20.9433 2.86936 23.2533 5.1794 23.2533 8.01882C23.2533 10.8582 20.9433 13.1683 18.1039 13.1683C15.2645 13.1683 12.9544 10.8582 12.9544 8.01882C12.9544 5.1794 15.2645 2.86936 18.1039 2.86936ZM14.5178 7.81284C14.9153 7.40603 15.5672 7.40088 15.9741 7.79739L17.1389 8.93851C17.2872 9.08373 17.5292 9.08167 17.6765 8.93851L19.9659 6.74072C20.3748 6.3473 21.0288 6.36069 21.4222 6.77059C21.8156 7.18049 21.8022 7.83241 21.3923 8.22686L19.1132 10.4143C18.6466 10.8747 18.0297 11.1054 17.4108 11.1054C16.7918 11.1054 16.1708 10.8747 15.697 10.4113L14.5322 9.27014C14.1254 8.87157 14.1203 8.21862 14.5178 7.81284Z"
                                                            fill="white" />
                                                    </svg></span>
                                                <span
                                                    class="tracking-label">{{ labels('front_messages.delivered', 'Delivered') }}</span>
                                            </div>
                                        @endif
                                        <div class="tracking-step-date-container">
                                            @foreach ($user_order_item['status'] as $status)
                                                <div class="tracking-step-date" style="width: {{ $statusDate }}%;">
                                                    {{ $status[1] }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    {{-- Old tracking steps --}}
                                    {{-- <div class="tracking-steps nav mt-5 mb-4 clearfix justify-content-center">
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
                                    </div> --}}
                                    <!-- End Tracking Steps -->
                                    <!-- Order Table -->
                                    {{-- <div class="table-bottom-brd table-responsive">
                                        <table class="table align-middle text-center order-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">{{ labels('front_messages.status', 'Status') }}
                                                    </th>
                                                    <th scope="col">{{ labels('front_messages.time', 'Time') }}
                                                    </th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user_order_item['status'] as $status)
                                                    <tr>
                                                        <td class="text-capitalize">
                                                            {{ str_replace('_', ' ', $status[0]) }}
                                                        </td>
                                                        <td>{{ $status[1] }}</td>
                                                        <td>
                                                            @if ($status[0] == 'shipped')
                                                                @php
                                                                    $aftershipData = $user_order_item['aftership_data'];
                                                                    $isValidJson = json_decode($aftershipData) !== null;
                                                                @endphp
                                                                <button class="btn btn-primary btn-sm"
                                                                    id="track_order"
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
                                    </div> --}}
                                @endforeach
                            @endforeach

                            @if ($user_order['type'] != 'digital_product')

                                <div class="mb-4">
                                    <div class="top-sec d-flex-justify-center justify-content-between mb-4">
                                        <h2 class="mb-0 order-details-subtitle">
                                            {{ labels('front_messages.address_details', 'Address Details') }}
                                        </h2>
                                    </div>
                                    <div class="mt-2">
                                        <div class="order-ship-address-container">
                                            <div>
                                                <span class="ship-address-icon">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.68698 20.777C10.0861 21.159 10.1001 21.793 9.71799 22.191C9.52193 22.396 9.25886 22.5 8.99578 22.5C8.74671 22.5 8.49764 22.408 8.30459 22.223L2.93908 17.087C-0.977021 13.172 -0.977021 6.828 2.92307 2.929C4.81261 1.04 7.32431 0 9.99606 0C12.6678 0 15.1795 1.04 17.0691 2.929C19.2297 5.089 20.275 8.072 19.9369 11.111C19.8758 11.66 19.3897 12.049 18.8325 11.995C18.2834 11.933 17.8883 11.439 17.9493 10.891C18.2194 8.458 17.3831 6.073 15.6547 4.344C14.1432 2.833 12.1337 2.001 9.99606 2.001C7.85846 2.001 5.8489 2.833 4.33747 4.344C1.2176 7.463 1.2176 12.539 4.33747 15.658L9.68798 20.778L9.68698 20.777ZM9.99606 14C7.78944 14 5.99494 12.206 5.99494 10C5.99494 7.794 7.78944 6 9.99606 6C12.2027 6 13.9972 7.794 13.9972 10C13.9972 12.206 12.2027 14 9.99606 14ZM11.9966 10C11.9966 8.897 11.0994 8 9.99606 8C8.89275 8 7.9955 8.897 7.9955 10C7.9955 11.103 8.89275 12 9.99606 12C11.0994 12 11.9966 11.103 11.9966 10ZM24 17V21C24 22.654 22.6536 24 20.9992 24H14.9975C13.343 24 11.9966 22.654 11.9966 21V17C11.9966 15.346 13.343 14 14.9975 14H20.9992C22.6536 14 24 15.346 24 17ZM15.5836 16L17.2911 17.707C17.6692 18.086 18.3284 18.086 18.7055 17.707L20.413 16H15.5836ZM21.9994 21V17.242L20.1199 19.121C19.5538 19.687 18.8005 20 17.9983 20C17.1961 20 16.4429 19.688 15.8757 19.121L13.9972 17.242V21C13.9972 21.552 14.4463 22 14.9975 22H20.9992C21.5503 22 21.9994 21.552 21.9994 21Z"
                                                            fill="white" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="order-ship-address">
                                                <p class="text-uppercase mb-1 pb-0">
                                                    {{ $user_order['order_recipient_person'] }}
                                                </p>
                                                <p><b>{{ $user_order['address'] }}</b></p>
                                                @if ($user_order['otp'] != '0' || $user_order['otp'] != null)
                                                    <p>{{ labels('front_messages.otp', 'OTP') }}:
                                                        {{ $user_order['otp'] }}</p>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                            <div class="my-2">
                                <div class="top-sec d-flex-justify-center justify-content-between mb-4">
                                    <h2 class="mb-0 order-details-subtitle">
                                        {{ labels('front_messages.price_details', 'Price Details') }}
                                    </h2>
                                </div>
                                <div class="mt-2 order-payment-details">
                                    <p class="order-payment-details-light">
                                        {{ labels('front_messages.payment_mode', 'Payment Mode') }}:
                                        <span
                                            class="text-uppercase">{{ $user_order['wallet_balance'] == $user_order['final_total'] ? 'Wallet' : $user_order['payment_method'] }}</span>
                                    </p>
                                    <p class="order-payment-details-light">
                                        {{ labels('front_messages.total_price', 'Total Price') }}
                                        <span>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['total'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                    </p>
                                    @if ($user_order['delivery_charge'] > 0)
                                        <p class="order-payment-details-light">
                                            {{ labels('front_messages.delivary_charge', 'Delivary Charge') }}
                                            <span>+{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['delivery_charge'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                        </p>
                                    @endif
                                    @if (isset($transaction['fee']) && $transaction['fee'] != 0)
                                        <p class="order-payment-details-light">
                                            {{ labels('front_messages.transaction_fee', 'Transaction Fee') }}
                                            <span>+{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $transaction['fee'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                        </p>
                                    @endif
                                    @if ($user_order['promo_discount'] > 0)
                                        <p class="order-payment-details-light">
                                            {{ labels('front_messages.promo_discount', 'Coupon Discount') }}
                                            <span>-{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['promo_discount'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                        </p>
                                    @endif
                                    @if ($user_order['wallet_balance'] != 0.0)
                                        <p class="order-payment-details-light">
                                            {{ labels('front_messages.wallet_balance_used', 'Wallet Balance Used') }}
                                            <span>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $user_order['wallet_balance'] * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                        </p>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="order-payment-final">
                                            {{ labels('front_messages.final_total', 'Final Total') }}<br>
                                            @php
                                                if (isset($transaction['fee']) && $transaction['fee'] != 0) {
                                                    $final_total = $user_order['final_total'] + $transaction['fee'];
                                                } else {
                                                    $final_total = $user_order['final_total'];
                                                }
                                            @endphp
                                            <span>{{ isset($currency_details) && !empty($currency_details) ? $currency_symbol . number_format((float) $final_total * $currency_details[0]->exchange_rate, 2) : '' }}</span>
                                        </p>
                                        <a href="{{ route('front_end.orders.generatInvoicePDF', $user_orders['order_data'][0]->id) }}"
                                            class="btn btn-secondary btn-sm instructions_files"><i
                                                class='bx bx-download me-1'></i>{{ labels('admin_labels.invoice', 'Invoice') }}
                                        </a>
                                    </div>
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


                                </div>
                            </div>
                            <!-- End Order Table -->
                        </div>
                    </div>
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

    {{-- Confirm received Modal --}}
    <div class="modal fade" id="confirmReceivedModal" tabindex="-1" aria-labelledby="confirmReceivedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmReceivedModalLabel">Item received confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Logistic shows that your package has been delivered. Please check your order and if you're
                        satisfied click the "Confirm" button to confirm receipt.</p>
                    <form id="confirmReceivedForm">
                        <div class="mb-3" id="confirmItemList">
                        </div>
                        <input type="hidden" id="order_item_id" value="{{ $transaction['transaction_type'] }}">
                        <input type="hidden" id="paymentType" value="{{ $transaction['type'] }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmReceived">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Відкриття модального вікна та завантаження списку товарів
        $(document).on("click", ".confirm_received_btn", function(e) {
            e.preventDefault();
            const itemId = $(this).data('item-id'); // Отримуємо itemId з кнопки
            const $modal = $('#confirmReceivedModal');
            const $itemList = $('#confirmItemList'); // Блок для списку товарів

            $itemList.html('<p>Loading...</p>');

            $modal.modal('show');

            $.ajax({
                url: '/orders/get-parcel-items',
                method: 'POST',
                data: {
                    order_item_id: itemId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Перевіряємо, чи є дані в відповіді
                    if (response.success && response.data) {
                        let html = '';

                        // Формуємо HTML для списку товарів
                        if (Array.isArray(response.data)) {
                            response.data.forEach(item => {
                                html += `
                                    <div class="form-check d-flex align-items-center mb-2">
                                        <input class="form-check-input me-2" type="checkbox" name="confirm_items[]"
                                            value="${item.id}" id="confirm_item_${item.id}">
                                        <img src="${item.image}" alt="${item.product_name}" class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                        <label class="form-check-label" for="confirm_item_${item.id}">
                                            ${item.product_name} ${item.variant_name ? ' - ' + item.variant_name : ''}
                                            (${item.price_formatted})
                                        </label>
                                    </div>
                                `;
                            });
                        } else {
                            // Якщо повертається один товар
                            html = `
                                <div class="form-check d-flex align-items-center mb-2">
                                    <input class="form-check-input me-2" type="checkbox" name="confirm_items[]"
                                        value="${response.data.id}" id="confirm_item_${response.data.id}" checked>
                                    <img src="${response.data.image}" alt="${response.data.product_name}" class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                    <label class="form-check-label" for="confirm_item_${response.data.id}">
                                        ${response.data.product_name} ${response.data.variant_name ? ' - ' + response.data.variant_name : ''}
                                        (${response.data.price_formatted})
                                    </label>
                                </div>
                            `;
                        }

                        $itemList.html(html);
                    } else {
                        $itemList.html('<p>Failed to load items. Please try again.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    $itemList.html(`<p>Error: ${xhr.status} - ${error}</p>`);
                    console.error('AJAX Error:', error);
                }
            });

            // Зберігаємо itemId у приховане поле форми для подальшого використання
            $('#order_item_id').val(itemId);
        });

        // Обробник кнопки "Confirm" у модальному вікні
        $(document).on("click", "#confirmReceived", function(e) {
            e.preventDefault();
            const $form = $('#confirmReceivedForm');
            const itemId = $('#order_item_id').val();
            const selectedItems = $form.find('input[name="confirm_items[]"]:checked')
                .map(function() {
                    return $(this).val(); // Значення — це parcel_item.id
                })
                .get();

            if (selectedItems.length === 0) {
                iziToast.error({
                    position: 'topRight',
                    title: "Error",
                    message: 'Please select at least one product for confirmation'
                });
                return;
            }

            $.ajax({
                url: '/orders/confirm-received',
                method: 'POST',
                data: {
                    item_id: itemId,
                    items: selectedItems,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        iziToast.success({
                            position: 'topRight',
                            title: "Success",
                            message: "Receipt of goods successfully confirmed!"
                        })
                        $('#confirmReceivedModal').modal('hide');
                        location.reload();
                    } else {
                        iziToast.error({
                            position: 'topRight',
                            title: "Error",
                            message: response.message || 'Something went wrong.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    iziToast.error({
                        position: 'topRight',
                        title: "Error",
                        message: 'Server error: ' + error
                    });
                    console.error('AJAX Error:', error);
                }
            });
        });


        // Out tracking details
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
                aftershipData = typeof trackingData === 'string' ? JSON.parse(trackingData) :
                    trackingData;
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
