<div id="page-content">
    <!--Page Header-->
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <!--End Page Header-->
    {{-- @dd($cart_data) --}}
    <div class="container-fluid">
        @if (count($cart_data) >= 1 || count($save_for_later) >= 1)
            @if (count($cart_data) >= 1)
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-8 main-col cart-box">

                        <!--Cart Form-->
                        <table class="table align-middle">
                            <thead class="cart-row cart-header small-hide">
                                <tr>
                                    <th class="action">&nbsp;</th>
                                    <th colspan="2" class="text-start">
                                        {{ labels('front_messages.product', 'Product') }}</th>
                                    <th class="text-center"></th>
                                    <th class="text-center">{{ labels('front_messages.price', 'Price') }}</th>
                                    <th class="text-center">{{ labels('front_messages.quantity', 'Quantity') }}</th>
                                    <th class="text-center">{{ labels('front_messages.total', 'Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $is_save_later_hide = 0;
                                    $is_remove_from_cart = 0;
                                    $for_checkout = 0;
                                @endphp
                                @foreach ($cart_data['cart_items'] as $cartItem)
                                    <x-utility.cart.CardOne :$cartItem :$is_save_later_hide :$is_remove_from_cart
                                        :$for_checkout />
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!--Cart Sidebar-->
                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 cart-footer">
                        <div class="cart-info sidebar-sticky">
                            <div class="cart-order-detail cart-col">
                                <div class="row g-0 pt-2 border-bottom">
                                    <span
                                        class="col-6 col-sm-6 cart-subtotal-title fs-6"><strong>{{ labels('front_messages.total', 'Total') }}</strong></span>
                                    <span
                                        class="col-6 col-sm-6 cart-subtotal-title fs-5 cart-subtotal text-end text-primary"><b
                                            class="money">{{ currentCurrencyPrice($cart_data['sub_total'], true) }}</b></span>
                                </div>

                                <p class="cart-shipping">Inclusive of all taxes & Shipping calculated at checkout</p>
                                <a href="{{ customUrl('cart/checkout') }}" wire:navigate id="cartCheckout"
                                    class="btn btn-secondary btn-lg my-4 checkout w-100">{{ labels('front_messages.proceed_to_checkout', 'Proceed To Checkout') }}</a>

                                <p class="cart-pay-methods-title">Payment methods</p>
                                <div class="cart-pay-methods">
                                    <img src="{{ asset('assets/img/icons/cards/visa.png') }}" width="45"
                                        height="30" alt="Visa">
                                    <img src="{{ asset('assets/img/icons/cards/mastercard.png') }}" width="45"
                                        height="30" alt="MasterCard">
                                    <img src="{{ asset('assets/img/icons/cards/maestro.png') }}" width="45"
                                        height="30" alt="Maestro">
                                    <img src="{{ asset('assets/img/icons/cards/american-express.png') }}"
                                        width="45" height="30" alt="American Express">
                                    <img src="{{ asset('assets/img/icons/cards/discover.png') }}" width="45"
                                        height="30" alt="Discover">
                                    <img src="{{ asset('assets/img/icons/cards/dinners-club.png') }}" width="45"
                                        height="30" alt="Dinners Club">
                                    <img src="{{ asset('assets/img/icons/cards/apple-pay.png') }}" width="45"
                                        height="30" alt="ApplePay">
                                    <img src="{{ asset('assets/img/icons/cards/google-pay.png') }}" width="45"
                                        height="30" alt="GooglePay">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End Cart Sidebar-->
                </div>
            @endif

            @if (count($save_for_later) >= 1)
                <section class="section product-slider pb-0">
                    <x-utility.section_header.sectionHeaderTwo :heading="$save_for_later['heading']" />
                    <!--Product Grid-->
                    <div class="grid-products grid-view-items">
                        <div class="row col-row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
                            @foreach ($save_for_later['cart_items'] as $item)
                                @php
                                    $product_img = dynamic_image($item['image'], 400);
                                @endphp
                                <div class="item col-item">
                                    <div class="product-box position-relative">
                                        <button wire:ignore type="button"
                                            wire:click="remove_from_cart({{ $item['id'] }})"
                                            class="btn remove-icon close-btn" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Remove"><i
                                                class="icon anm anm-times-r"></i></button>
                                        <div class="product-image">
                                            <a href="{{ customUrl('products/' . $item['slug']) }}"
                                                class="product-img rounded-0 save_later_product_img">
                                                <img class="primary rounded-0 blur-up lazyload"
                                                    data-src="{{ $product_img }}" src="{{ $product_img }}"
                                                    alt="{{ $item['name'] }}" title="{{ $item['name'] }}" />
                                                <img class="hover rounded-0 blur-up lazyload"
                                                    data-src="{{ $product_img }}" src="{{ $product_img }}"
                                                    alt="{{ $item['name'] }}" title="{{ $item['name'] }}" />
                                            </a>
                                        </div>
                                        <div class="product-details text-center">
                                            <div class="product-name">
                                                <a
                                                    href="{{ customUrl('products/' . $item['slug']) }}">{{ $item['name'] }}</a>
                                            </div>
                                            <div class="product-price">
                                                <span
                                                    class="price old-price">{{ currentCurrencyPrice($item['price'], true) }}</span><span
                                                    class="price">{{ currentCurrencyPrice($item['special_price'], true) }}</span>
                                            </div>
                                            <div class="button-action mt-3">
                                                <div class="addtocart-btn">
                                                    <button wire:click="move_to_cart({{ $item['id'] }})"
                                                        class="btn btn-md">
                                                        <span
                                                            class="text">{{ labels('front_messages.move_to_cart', 'Move To Cart') }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </section>
            @endif

            @if (!empty($related_product['product']))
                <!--Related Products-->
                <section class="section product-slider pb-0">
                    <div class="">
                        <x-utility.section_header.sectionHeaderTwo :heading="$related_product_heading" />
                        <!--Product Grid-->
                        <div wire:ignore class="swiper style1-mySwiper gp15 arwOut5 hov-arrow grid-products">
                            <div class="swiper-wrapper">
                                @foreach ($related_product['product'] as $details)
                                    <div class="swiper-slide ">
                                        <x-utility.cards.productCardOne :$details />
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                        <!--End Product Grid-->
                    </div>
                </section>
                <!--End Related Products-->
            @endif
        @else
            @php
                $title =
                    '<strong>' .
                    labels('front_messages.sorry', 'SORRY ') .
                    '</strong>' .
                    labels('front_messages.cart_is_currently_empty', 'Cart is currently empty');
            @endphp
            <x-utility.others.not-found :$title />
        @endif
    </div>
    <!--End Main Content-->

</div>
