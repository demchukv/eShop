@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.language', 'Language') }}
@endsection
@section('content')

    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.language', 'Language') }}</h3>
            <p class="sub_title">
                {{ labels('admin_labels.track_and_manage_language', 'Track and Manage Language') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.language', 'Language') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                {{ labels('admin_labels.language', 'Language') }}</h5>
            <div class="row">
                <form action="{{ route('web_language.store') }}" class="submit_form" enctype="multipart/form-data"
                    method="POST">
                    @csrf
                    <div class="col-xl">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"
                                        for="language">{{ labels('admin_labels.language', 'Language') }}</label>
                                    <input type="text" class="form-control" id="basic-default-fullname"
                                        placeholder="English" name="language" value="{{ old('language') }}">

                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"
                                        for="code">{{ labels('admin_labels.code', 'Code') }}</label>
                                    <input type="text" class="form-control" id="basic-default-fullname" placeholder="en"
                                        name="code" value="{{ old('code') }}">

                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="is_rtl" class="col-form-label">{{ labels('admin_labels.is_rtl', 'Is RTL') }}?
                                    <span class='text-asterisks text-sm'>*</span></label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_rtl" class="form-check-input mx-2">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit"
                                    class="btn btn-primary submit_button">{{ labels('admin_labels.add_language', 'Add Language') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="form-group col-md-12 mt-4">
                <div class="mb-3">
                    <div class="col-md-12 text-center mb-2">
                        <h4 class="h4">Labels</h4>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="current-lang" value="{{ $current_language_code }}" />

                        <form class="mb-3 submit_form" action="languages/savelabel" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3 col-md-12">
                                <div class="row custom-table-header">
                                    <div class="col-md-12 custom-col d-none">
                                        <label>Select Language</label>
                                        <select class="form-control form-select" name="langcode" id="language-settings">
                                            @foreach ($languages as $language)
                                                <option value={{ $language->code }}>
                                                    {{ ucfirst($language->language) }} -
                                                    {{ strtoupper($language->code) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                                id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ isset($current_language[0]->language) && !empty($current_language[0]->language) ? ucfirst($current_language[0]->language) : '' }}
                                                -
                                                {{ isset($current_language_code) && !empty($current_language_code) ? $current_language_code : '' }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                                                @foreach ($languages as $language)
                                                    <li>
                                                        <a class="dropdown-item{{ $current_language_code === $language->code ? ' active' : '' }}"
                                                            href="{{ route('front.set-language', $language->code) }}"
                                                            id="lang-{{ $language->code }}">
                                                            {{ $language->language }} - {{ $language->code }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                            $labels = config('labels');
                            @endphp
                            <div class="row">
                                @php
                                    $labels = [
                                        'home',
                                        'products',
                                        'change',
                                        'sellers',
                                        'seller',
                                        'stores',
                                        'contact_us',
                                        'faqs',
                                        'register',
                                        'username',
                                        'mobile',
                                        'otp',
                                        'password',
                                        'confirm_password',
                                        'or',
                                        'i_agree_to_term_and_policy',
                                        'or_sign_up_with',
                                        'facebook',
                                        'google',
                                        'login_now',
                                        'login',
                                        'already_have_an_account',
                                        'sign_in',
                                        'if_you_have_an_account_with_us_please_log_in',
                                        'mobile_number',
                                        'remember_me',
                                        'sign_in_with_social_account',
                                        'dont_have_an_account',
                                        'sign_up_now',
                                        'my_account',
                                        'wishlist',
                                        'compare',
                                        'sign_out',
                                        'need_help',
                                        'follow_us',
                                        'turms_and_conditions',
                                        'about_us',
                                        'shipping_policy',
                                        'return_refund_policy',
                                        'support_center',
                                        'support',
                                        'terms_and_condition',
                                        'privacy_policy',
                                        'lets_get_in_touch',
                                        'name',
                                        'subject',
                                        'message',
                                        'send_message',
                                        'address',
                                        'stay_connected',
                                        'dashboard',
                                        'addresses',
                                        'notifications',
                                        'transactions',
                                        'wallet',
                                        'favorites',
                                        'profile',
                                        'orders',
                                        'address_is_not_added',
                                        'add_address',
                                        'order_comment',
                                        'pay_with_wallet',
                                        'payment_methods',
                                        'cash_on_delivary',
                                        'apply_promocode',
                                        'apply',
                                        'remove',
                                        'add_promo',
                                        'order_summary',
                                        'image',
                                        'qty',
                                        'price',
                                        'subtotal',
                                        'tax',
                                        'delivery_charge',
                                        'coupon_discount',
                                        'wallet_balance_used',
                                        'total',
                                        'place_order',
                                        'select_address',
                                        'Select',
                                        'close',
                                        'add_promo',
                                        'go_back',
                                        'sorry',
                                        'cart_is_currently_empty',
                                        'valid_minimum_order_amount_of',
                                        'popular_categories',
                                        'category',
                                        'explore_categories',
                                        'popular_brands',
                                        'explore_brands',
                                        'view_more',
                                        'shop_now',
                                        'explore_now',
                                        'quick_view',
                                        'add_to_compare',
                                        'add_to_Wishlist',
                                        'add_to_cart',
                                        'discount',
                                        'discover_now',
                                        'phone',
                                        'email',
                                        'customer_services',
                                        'informations',
                                        'what_are_you_looking_for?',
                                        'trending_now',
                                        'You_dont_have_any_items_in_your_search',
                                        'search_product_for',
                                        'buy_it_now',
                                        'remove_from_wishlist',
                                        'no_guarantee',
                                        'no_warranty',
                                        'non_cancelable',
                                        'availability',
                                        'in_stock',
                                        'out_of_stock',
                                        'made_in',
                                        'product_type',
                                        'sku',
                                        'ticket_type',
                                        'description',
                                        'additional_information',
                                        'reviews',
                                        'related_products',
                                        'products_related_to',
                                        'frequently_asked_questions',
                                        'search',
                                        'basic_questions',
                                        'payments_privacy',
                                        'exchanges_&_returns',
                                        'shipping_&_Orders',
                                        'account_settings',
                                        'search_result_for',
                                        'no_result_for',
                                        'hello',
                                        'account_information',
                                        'contact_information',
                                        'edit',
                                        'change_password',
                                        'address_book',
                                        'default_billing_address',
                                        'no_default_address_selected',
                                        'add_new',
                                        'delivery_address_not_added',
                                        'default',
                                        'address_details',
                                        'address_type',
                                        'select_address_type',
                                        'office',
                                        'alternative_mobile_number',
                                        'landmark',
                                        'city',
                                        'post_code',
                                        'state',
                                        'country',
                                        'latitude',
                                        'longitude',
                                        'order_id',
                                        'product_details',
                                        'status',
                                        'view',
                                        'all',
                                        'awaiting',
                                        'received',
                                        'processing',
                                        'shipped',
                                        'delivered',
                                        'canceled',
                                        'my_orders',
                                        'log_out',
                                        'my_wishlist',
                                        'product_id',
                                        'action',
                                        'withdraw',
                                        'add',
                                        'wallet_transaction',
                                        'withdraw_request',
                                        'transaction_id',
                                        'transaction_type',
                                        'pay_transaction_id',
                                        'amount',
                                        'date',
                                        'company_name',
                                        'edit_profile_details',
                                        'save_profile',
                                        'login_details',
                                        'current_password',
                                        'new_password',
                                        'verify_password',
                                        'save_changes',
                                        'product',
                                        'quantity',
                                        'proceed_to_checkout',
                                        'save_for_later',
                                        'cart_empty',
                                        'move_to_cart',
                                        'title',
                                        'created_date',
                                        'updated_date',
                                        'opened',
                                        'resolved',
                                        'closed',
                                        'reopened',
                                        'in_review',
                                        'add_new_ticket',
                                        'select_ticket',
                                        'time_slot',
                                        'time',
                                        'tags',
                                        'verification_code',
                                        'tickets',
                                        'totle',
                                        'wallet_refill',
                                        'add_amount',
                                        'withdrawal',
                                        'withdrawal_amount',
                                        'payment_details',
                                        'no_offers_found',
                                        'orders_details',
                                        'order_id',
                                        'product_name',
                                        'cancelable_till',
                                        'total_price',
                                        'final_total',
                                        'cancle',
                                        'return',
                                        'processed',
                                        'address_details',
                                        'name',
                                        'address',
                                        'price_details',
                                        'payment_mode',
                                        'delivary_charge',
                                        'download_invoice',
                                        'downlaod',
                                        'wallet_balance_used',
                                        'store_information',
                                        'order_processsed_successfully',
                                        'continue_shopping',
                                        'failed_to_processed_order',
                                        'thank_you_for_shopping',
                                        'wallet_refill_successfully',
                                        'back_to_wallet',
                                        'payment_failed',
                                        'results_for',
                                        'results_found_for',
                                        'close_all_tabs',
                                        'show_all_tabs',
                                        'filter_by',
                                        'filter',
                                        'no_product_found',
                                        'view_as',
                                        'showing',
                                        'per_page',
                                        'Featured',
                                        'sort_by',
                                        'top_rated',
                                        'price_low_to_high',
                                        'price_high_to_low',
                                        'old_to_new',
                                        'new_to_old',
                                        'customer_reviews',
                                        'total_reviews',
                                        'enter_your_mobile_number',
                                        'forgot_password',
                                        'back_to_login',
                                        'enter_verification_code',
                                        'enter_new_password',
                                        'send_otp',
                                        'verify_code',
                                        'ratings',
                                        'rating',
                                        'seller_dont_have_any_products',
                                        'no_seller_found',
                                        'average_rating',
                                        'view_all',
                                        'write_review',
                                        'add_image_or_video',
                                        'no_products_in_cart',
                                        'total',
                                        'check_out',
                                        'view_cart',
                                        'your_cart',
                                        'view_more_details',
                                        'delete',
                                        'offers',
                                        'no_blog_found',
                                        'read_more',
                                        'Show',
                                        'blogs',
                                        'shop_now',
                                        'combo_product',
                                        'combo_products',
                                        'products_included_in_combo',
                                        'regular_products',
                                        'live_customer_support',
                                        'compare_is_currently_empty',
                                        'digital_product',
                                        'edit_review',
                                        'select_promo',
                                        'wishlist_is_empty',
                                        'no_orders_have_been_placed',
                                        'clear',
                                        'please_choose_store',
                                        'pick_store_that_suits_your_requirements',
                                        'password_recovery',
                                        'password_reset',
                                        'check_product_deliverability',
                                        'check',
                                        'pincode',
                                        'i_agree_with_the',
                                        'related_products',
                                        'products_you_may_like',
                                        'cart',
                                        'checkout',
                                    ];
                                @endphp
                                @foreach ($labels as $label)
                                    {!! create_front_label($label, ucwords(str_replace('_', ' ', $label))) !!}
                                @endforeach
                            </div>
                            <div class="mt-2 d-flex justify-content-end">
                                <button type="submit"
                                    class="btn btn-primary me-2 submit_button">{{ labels('admin_labels.save', 'Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
