@php
    $store_id = session('store_id');
    $store_details = getCurrentStoreData($store_id);
    $store_details = json_decode($store_details) ?? '';
@endphp
<div>
    <input type="hidden" id="store-primary-color" name="store-primary-color"
        value="{{ $store_details[0]->primary_color ?? '#041632' }}">
    <input type="hidden" id="store-secondary-color" name="store-secondary-color"
        value="{{ $store_details[0]->secondary_color ?? '#f4a51c' }}">
    <input type="hidden" id="store-link-active-color" name="store-link-active-color"
        value="{{ $store_details[0]->active_color ?? '#041632' }}">
    <input type="hidden" id="store-link-hover-color" name="store-link-hover-color"
        value="{{ $store_details[0]->hover_color ?? '#f4a51c' }}">
    <div class="top-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-6 col-sm-6 col-md-3 col-lg-4 text-left d-flex">
                    <ion-icon wire:ignore class="fs-5 me-2" name="call-outline"></ion-icon><a
                        href="tel:{{ $settings->support_number }}">{{ $settings->support_number }}</a>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-4 text-center d-none d-md-block">
                    {{ $settings->app_short_description }}
                </div>
                <div class="col-6 col-sm-6 col-md-3 col-lg-4 text-right d-flex align-items-center justify-content-end">
                    <div class="select-wrap language-picker float-start">
                        <ul class="default-option">
                            <li>
                                <div class="option english">
                                    <span>{{ session('locale') ?? ($languages[0]->code ?? 'en') }}</span>
                                </div>
                            </li>
                        </ul>
                        <ul class="select-ul">
                            @foreach ($languages as $language)
                                <li class="option english changeLang" data-lang-code="{{ $language->code }}">
                                    <div>
                                        <span>{{ $language->code }}</span>
                                    </div>
                                </li>
                            @endforeach
                            <li>
                        </ul>
                    </div>
                    <div class="select-wrap currency-picker float-start">
                        <ul class="default-option">
                            <li>
                                <div class="option USD">
                                    <span>{{ session('currency') ?? ($system_settings['currency_setting']['code'] ?? 'USD') }}</span>
                                </div>
                            </li>
                        </ul>
                        <ul class="select-ul">
                            @foreach ($currencies as $currency)
                                <li class="option USD changeCurrency" data-currency-code="{{ $currency->code }}">
                                    <div>
                                        <span>{{ $currency->code }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Top Header-->

    <!--Header-->
    <header wire:ignore.self class="header d-flex align-items-center header-1 header-fixed">
        <div id="app_url" data-app-url="{{ route('home') }}"></div>
        <div class="container-fluid">
            <div class="row">
                {{-- @dd($store_details) --}}
                @php
                    $img =
                        !empty($store_details[0]->half_store_logo) &&
                        file_exists(
                            public_path(config('constants.STORE_IMG_PATH') . $store_details[0]->half_store_logo),
                        )
                            ? getImageUrl($store_details[0]->half_store_logo, '', '', 'image', 'STORE_IMG_PATH')
                            : getImageUrl($settings->logo);
                    $img = dynamic_image($img, 150);
                @endphp

                <!--Logo-->
                <div class="logo col-5 col-sm-3 col-md-3 col-lg-2 align-self-center">
                    <a wire:navigate class="logoImg" href="{{ customUrl('home') }}"><img src="{{ $img }}"
                            alt="{{ $settings->site_title }}" title="{{ $settings->site_title }}" /></a>
                </div>
                <!--End Logo-->

                <!--Menu-->
                <div class="col-1 col-sm-1 col-md-1 col-lg-8 align-self-center d-menu-col">
                    <nav class="navigation" id="AccessibleNav">
                        <ul id="siteNav" class="site-nav medium center">
                            <li class="lvl1 parent dropdown"><a wire:navigate
                                    class="{{ request()->is('/') ? 'active' : '' }}"
                                    href="{{ customUrl('home') }}">{{ labels('front_messages.home', 'Home') }}</a>
                            </li>
                            <li class="lvl1 parent megamenu"><a wire:navigate
                                    class="{{ request()->is('products*') ? 'active' : '' }}"
                                    href="{{ customUrl('products') }}">{{ labels('front_messages.products', 'Products') }}
                                </a>
                                <div class="megamenu">
                                    <div class="container">
                                        <div class="row">
                                            @foreach ($categories as $index => $category)
                                                @if ($index < 4)
                                                    <!-- Обмежуємо до 4 колонок -->
                                                    <div class="col-megamenu col-md-3">
                                                        <h6 class="menu-title">{{ $category['name'] }}</h6>
                                                        <ul class="list-unstyled">
                                                            @foreach ($category['children'] as $subCategory)
                                                                <li>
                                                                    <a
                                                                        href="{{ customUrl('products?category=' . $subCategory['slug']) }}">{{ $subCategory['name'] }}</a>
                                                                    @if (!empty($subCategory['children']))
                                                                        <ul class="list-unstyled">
                                                                            @foreach ($subCategory['children'] as $childCategory)
                                                                                <li>
                                                                                    <a
                                                                                        href="{{ customUrl('products?category=' . $childCategory['slug']) }}">{{ $childCategory['name'] }}</a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            </li>
                            {{-- <li class="lvl1 parent megamenu"><a wire:navigate
                                    class="{{ request()->is('combo-products*') ? 'active' : '' }}"
                                    href="{{ customUrl('combo-products') }}">{{ labels('front_messages.combo_products', 'Combo Products') }}
                                </a>
                            </li> --}}
                            <li class="lvl1 parent megamenu"><a wire:navigate
                                    class="{{ request()->is('offers*') ? 'active' : '' }}"
                                    href="{{ customUrl('offers') }}">{{ labels('front_messages.offers', 'Offers') }}
                                </a>
                            </li>
                            <li class="lvl1 parent megamenu"><a wire:navigate
                                    class="{{ request()->is('sellers*') ? 'active' : '' }}"
                                    href="{{ customUrl('sellers') }}">{{ labels('front_messages.sellers', 'Sellers') }}
                                </a>
                            </li>
                            <li class="lvl1 parent dropdown"><a wire:navigate
                                    class="{{ request()->is('compare*') ? 'active' : '' }}"
                                    href="{{ customUrl('compare') }}">{{ labels('front_messages.compare', 'Compare') }}
                                </a>
                            </li>
                            <li class="lvl1 parent dropdown"><a wire:navigate
                                    class="{{ request()->is('blogs*') ? 'active' : '' }}"
                                    href="{{ customUrl('blogs') }}">{{ labels('front_messages.blogs', 'Blogs') }}
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!--End Menu-->

                <!--Right Icon-->
                <div class="col-7 col-sm-9 col-md-9 col-lg-2 align-self-center icons-col text-right">
                    <!--Search-->
                    <div class="search-parent iconset">
                        <div class="site-search" title="Search">
                            <a href="#;" class="search-icon clr-none" data-bs-toggle="offcanvas"
                                data-bs-target="#search-drawer"><ion-icon wire:ignore class="fs-5"
                                    name="search-outline"></ion-icon></a>
                        </div>
                    </div>
                    <!--End Search-->
                    <!--Account-->
                    <div class="account-parent iconset">
                        <div class="account-link" title="Account"><ion-icon wire:ignore class="fs-5"
                                name="person-outline"></ion-icon></div>
                        <div id="accountBox">
                            <div class="customer-links">
                                <ul class="m-0">
                                    @auth

                                        <li><a href="{{ customUrl('my-account') }}" wire:navigate><ion-icon
                                                    name="person-outline"
                                                    class="me-1"></ion-icon>{{ labels('front_messages.my_account', 'My Account') }}</a>
                                        </li>
                                        <li><a href="{{ customUrl('orders') }}" wire:navigate><ion-icon
                                                    name="cube-outline"
                                                    class="me-1"></ion-icon>{{ labels('front_messages.my_orders', 'My Orders') }}</a>
                                        </li>
                                        <li><a href="{{ customUrl('my-account/wallet') }}" wire:navigate><ion-icon
                                                    name="wallet-outline"
                                                    class="me-1"></ion-icon>{{ labels('front_messages.wallet', 'Wallet') }}</a>
                                        </li>
                                        <li><a class="logout"><ion-icon wire:ignore name="log-out-outline"
                                                    class="me-1"></ion-icon>{{ labels('front_messages.sign_out', 'Sign out') }}</a>
                                        </li>
                                    @else
                                        <li><a href="{{ customUrl('login') }}" wire:navigate><ion-icon wire:ignore
                                                    name="log-in-outline"
                                                    class="me-1"></ion-icon>{{ labels('front_messages.sign_in', 'Sign In') }}</a>
                                        </li>
                                        <li><a href="{{ customUrl('register') }}" wire:navigate><ion-icon
                                                    name="person-outline"
                                                    class="me-1"></ion-icon>{{ labels('front_messages.register', 'Register') }}</a>
                                        </li>
                                    @endauth
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--End Account-->
                    @php
                        $cart_count = '0';
                    @endphp
                    @auth
                        @php
                            $user_id = auth()->id() ?? 0;
                            $store_id = session('store_id') ?? '';
                            $favorites = getFavorites(user_id: $user_id, store_id: $store_id);
                            $cart_count = getCartCount($user_id, $store_id);
                        @endphp
                        <!--Wishlist-->
                        <div class="wishlist-link iconset" title="Wishlist"><a wire:navigate
                                href="{{ customUrl('my-account.favorites') }}" class="text-black"><ion-icon wire:ignore
                                    class="fs-5" name="heart-outline"></ion-icon><span
                                    class="wishlist-count">{{ $favorites['favorites_count'] }}</span></a>
                        </div>
                        <!--End Wishlist-->
                    @endauth
                    <!--Minicart-->
                    <div class="header-cart iconset" title="Cart">
                        <a href="#;" class="header-cart btn-minicart clr-none" data-bs-toggle="offcanvas"
                            data-bs-target="#minicart-drawer"><ion-icon wire:ignore class="fs-5"
                                name="cart-outline"></ion-icon><span
                                class="cart-count">{{ $cart_count }}</span></a>
                    </div>
                    <!--End Minicart-->
                    <!--Mobile Toggle-->
                    <button type="button"
                        class="iconset pe-0 menu-icon js-mobile-nav-toggle mobile-nav--open d-lg-none"
                        title="Menu"><ion-icon wire:ignore class="fs-5" name="menu"></ion-icon></button>
                    <!--End Mobile Toggle-->
                </div>
                <!--End Right Icon-->
            </div>
        </div>
    </header>
    <!--End Header-->
    <!--Mobile Menu-->
    <div class="mobile-nav-wrapper" role="navigation">
        <div class="closemobileMenu">{{ labels('front_messages.close_menu', 'Close Menu') }}<ion-icon
                name="close-outline" class="icon"></ion-icon></div>
        <ul id="MobileNav" class="mobile-nav">
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('home') }}">{{ labels('front_messages.home', 'Home') }}</a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('products') }}">{{ labels('front_messages.products', 'Products') }} </a>
            </li>
            {{-- <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('combo-products') }}">{{ labels('front_messages.combo_products', 'Combo Products') }}
                </a>
            </li> --}}
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('compare') }}">{{ labels('front_messages.compare', 'Compare') }}
                </a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('offers') }}">{{ labels('front_messages.offers', 'Offers') }} </a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('sellers') }}">{{ labels('front_messages.sellers', 'Sellers') }} </a>
            </li>
            {{-- <li class="lvl1 parent megamenu">
                <a href="#;"class="header-cart btn-minicart clr-none" data-bs-toggle="offcanvas"
                    data-bs-target="#minicart-drawer-stores">{{ labels('front_messages.stores', 'Stores') }}</a>
            </li> --}}
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('contact_us') }}">{{ labels('front_messages.contact_us', 'Contact Us') }}
                </a>
            </li>
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('faqs') }}">{{ labels('front_messages.faqs', 'FAQs') }} </a>
            </li>
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('blogs') }}">{{ labels('front_messages.blogs', 'Blogs') }} </a>
            </li>

            <li class="mobile-menu-bottom">
                <div class="mobile-links">
                    <ul class="list-inline d-inline-flex flex-column w-100">
                        @auth
                            <li><a href="{{ customUrl('my-account') }}" wire:navigate
                                    class="d-flex align-items-center"><ion-icon wire:ignore name="person-outline"
                                        class="me-1"></ion-icon>{{ labels('front_messages.my_account', 'My Account') }}</a>
                            </li>
                        @else
                            <li><a href="{{ customUrl('login') }}" wire:navigate
                                    class="d-flex align-items-center"><ion-icon name="log-in-outline"
                                        class="me-1"></ion-icon>{{ labels('front_messages.sign_in', 'Sign In') }}</a>
                            </li>
                            <li><a href="{{ customUrl('register') }}" wire:navigate
                                    class="d-flex align-items-center"><ion-icon wire:ignore name="person-outline"
                                        class="me-1"></ion-icon>{{ labels('front_messages.register', 'Register') }}</a>
                            </li>
                        @endauth
                        <li class="title h5">{{ labels('front_messages.need_help', 'Need Help?') }}</li>
                        <li><a href="tel:401234567890" class="d-flex align-items-center"><ion-icon
                                    name="call-outline" class="me-1"></ion-icon>
                                {{ $settings->support_number }}</a></li>
                        <li><a href="mailto:info@example.com" class="d-flex align-items-center"><ion-icon
                                    name="mail-outline" class="me-1"></ion-icon> {{ $settings->support_email }}</a>
                        </li>
                    </ul>
                </div>
                <div class="mobile-follow mt-2">
                    <h5 class="title">{{ labels('front_messages.follow_us', 'Follow Us') }}</h5>
                    <ul class="list-inline social-icons mt-3">
                        @if ($settings->twitter_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->twitter_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter"><ion-icon
                                        class="fs-5" name="logo-twitter"></ion-icon></a></li>
                        @endif

                        @if ($settings->facebook_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->facebook_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook"><ion-icon
                                        class="fs-5" name="logo-facebook"></ion-icon></a></li>
                        @endif

                        @if ($settings->instagram_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->instagram_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Instagram"><ion-icon
                                        class="fs-5" name="logo-instagram"></ion-icon></a></li>
                        @endif

                        @if ($settings->youtube_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->youtube_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Youtube"><ion-icon
                                        class="fs-5" name="logo-youtube"></ion-icon></a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    {{-- search --}}
    <livewire:header.SearchProduct />
    @if (isset($stores) && count($stores) >= 2)
        <div class="sticky-stores">
            <div class="stores-main">
                <div wire:ignore class="in-out-store-arrow store-show">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </div>
                <div class="stores-container">
                    @foreach ($stores as $store)
                        @php
                            $store_img = route('front_end.dynamic_image', [
                                'url' => getMediaImageUrl($store->image, 'STORE_IMG_PATH'),
                                'width' => 50,
                                'quality' => 90,
                            ]);
                        @endphp
                        <div class="store-box select-store {{ session('store_id') == $store->id ? 'store-active' : '' }}"
                            title="{{ $store->name }}" data-store-id="{{ $store->id }}"
                            data-store-name="{{ $store->name }}" data-store-slug="{{ $store->slug }}"
                            data-store-image="{{ $store_img }}">
                            <img src="{{ $store_img }}" alt="{{ $store->name }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <livewire:pages.model-cart />
    @if (isset($stores) &&
            count($stores) >= 2 &&
            url()->full() == customUrl(url()->full()) &&
            session()->get('show_store_popup'))
        <div class="newsletter-modal modal fade" id="store_select_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-body p-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <div class="newsletter-wrap d-flex flex-column">
                            <div class="newsltr-text text-center store-popup-box">
                                <div class="wraptext mw-100">
                                    <h6 class="title text-transform-none">
                                        {{ labels('front_messages.please_choose_store.', 'Please choose a store.') }}
                                    </h6>
                                    <p class="text">
                                        {{ labels('front_messages.pick_store_that_suits_your_requirements', 'Pick a Store that Suits Your Requirements.') }}
                                    </p>
                                    <div class="collection-style1 row row-cols-2 row-cols-lg-3 mt-0">
                                        @if (count($stores) >= 1)
                                            @foreach ($stores as $store)
                                                @php
                                                    $store_img = route('front_end.dynamic_image', [
                                                        'url' => getMediaImageUrl($store->image, 'STORE_IMG_PATH'),
                                                        'width' => 400,
                                                        'quality' => 90,
                                                    ]);
                                                @endphp
                                                <div class="category-item zoomscal-hov">
                                                    <a data-store-id="{{ $store->id }}"
                                                        data-store-name="{{ $store->name }}"
                                                        data-store-slug="{{ $store->slug }}"
                                                        data-store-image="{{ $store_img }}"
                                                        class="category-link select-store clr-none cursor-pointer">
                                                        <div class="zoom-scal zoom-scal-nopb brands-image">
                                                            <img class="blur-up lazyload w-100"
                                                                data-src="{{ $store_img }}"
                                                                src="{{ $store_img }}"
                                                                alt="{{ $store->name }}"
                                                                title="{{ $store->name }}" />
                                                        </div>
                                                        <div
                                                            class="details mt-3 d-flex justify-content-center align-items-center">
                                                            <h4 class="category-title mb-0">
                                                                {{ $store->name }}
                                                            </h4>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    {{-- <div class="customCheckbox checkboxlink clearfix justify-content-center">
                                    <input id="dontshow" name="newsPopup" type="checkbox" />
                                    <label for="dontshow" class="mb-0">Don't show this popup again</label>
                                </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <style>
        #nprogress .bar {
            background: <?=$store_details[0]->secondary_color ?? '#f4a51c' ?> !important;
            z-index: 1111;
        }

        .swiper-pagination-bullet-active-main {
            background: <?=$store_details[0]->secondary_color ?? '#f4a51c' ?> !important;
        }
    </style>
    @if (isset($stores) &&
            count($stores) >= 2 &&
            url()->full() == customUrl(url()->full()) &&
            session()->get('show_store_popup'))
        @php
            session()->put('show_store_popup', false);
        @endphp
        <script>
            setTimeout(function() {
                $('#store_select_modal').modal("show");
            }, 2000);
        </script>
    @endif
</div>
