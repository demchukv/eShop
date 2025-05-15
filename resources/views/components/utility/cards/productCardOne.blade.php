@props(['details'])
{{-- @dd($details); --}}
<div class="item col-item">
    @if ($details->type != 'combo-product')
        <div class="product-box">
            <div class="product-image m-0">
                <a wire:navigate href="{{ customUrl('products/' . $details->slug) }}"
                    class="all-product-img product-img slider-link"
                    data-link="{{ customUrl('products/' . $details->slug) }}">
                    <img class="blur-up lazyload" src="{{ dynamic_image($details->image, 400) }}" alt="Product"
                        title="{{ $details->name }}" width="625" height="808" />
                </a>
                <div class="button-set style1">
                    <a href="#quickview-modal" class="btn-icon quickview quick-view-modal" data-bs-toggle="modal"
                        data-bs-target="#quickview_modal" data-product-id="{{ $details->id }}">
                        <span class="icon-wrap d-flex-justify-center h-100 w-100" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="Quick View"><ion-icon name="search-outline"
                                class="fs-6"></ion-icon><span
                                class="text">{{ labels('front_messages.quick_view', 'Quick View') }}</span>
                    </a>
                    <a class="btn-icon wishlist card_fav_btn {{ $details->is_favorite == 1 ? 'remove-favorite' : 'add-favorite' }}"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Add To Wishlist"
                        data-product-id="{{ $details->id }}" data-product-type="regular"><ion-icon
                            name="{{ $details->is_favorite == 1 ? 'heart' : 'heart-outline' }}"
                            class="{{ $details->is_favorite == 1 ? 'text-danger' : '' }} fs-6"></ion-icon><span
                            class="text">{{ labels('front_messages.add_to_Wishlist', 'Add To Wishlist') }}</span></a>

                    <a class="btn-icon compare add-compare" data-product-id="{{ $details->id }}"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Add to Compare"><ion-icon
                            name="shuffle-outline" class="fs-6"></ion-icon><span
                            class="text">{{ labels('front_messages.add_to_compare', 'Add to Compare') }}</span></a>
                </div>
                @if ($details->type == 'variable_product')
                    <div class="button-action mt-2">
                        <div class="addtocart-btn">
                            <a href="#quickview-modal" class="add-to-cart-button-style-round quickview quick-view-modal"
                                data-bs-toggle="modal" data-bs-target="#quickview_modal"
                                data-product-id="{{ $details->id }}" data-product-variant-id=''>
                                <svg width="91" height="91" viewBox="0 0 91 91" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_f_3787_1792)">
                                        <circle cx="45.5" cy="45.2031" r="37.5" fill="#7EBB44" />
                                    </g>
                                    <circle cx="45.5" cy="45.2031" r="37.5" fill="#7EBB44" />
                                    <path
                                        d="M37.6875 57.4531C39.549 57.4531 41.0625 58.9666 41.0625 60.8281C41.0625 62.6896 39.549 64.2031 37.6875 64.2031C35.826 64.2031 34.3125 62.6896 34.3125 60.8281C34.3125 58.9666 35.826 57.4531 37.6875 57.4531ZM53.3125 57.4531C55.174 57.4531 56.6875 58.9666 56.6875 60.8281C56.6875 62.6896 55.174 64.2031 53.3125 64.2031C51.451 64.2031 49.9375 62.6896 49.9375 60.8281C49.9375 58.9666 51.451 57.4531 53.3125 57.4531ZM37.6875 59.5156C36.9632 59.5156 36.375 60.1051 36.375 60.8281C36.375 61.5512 36.9632 62.1406 37.6875 62.1406C38.4118 62.1406 39 61.5512 39 60.8281C39 60.1051 38.4118 59.5156 37.6875 59.5156ZM53.3125 59.5156C52.5882 59.5156 52 60.1051 52 60.8281C52 61.5512 52.5882 62.1406 53.3125 62.1406C54.0368 62.1406 54.625 61.5512 54.625 60.8281C54.625 60.1051 54.0368 59.5156 53.3125 59.5156ZM29.4971 26.2031L29.6895 26.208C31.6713 26.3003 33.3393 27.809 33.6133 29.7891L33.9834 32.4531H46.2812C46.8506 32.4531 47.3125 32.9151 47.3125 33.4844C47.3125 34.0537 46.8506 34.5156 46.2812 34.5156H34.2695L36.1533 48.0781H55.9062C58.384 48.0781 60.5374 46.3137 61.0234 43.8838L62.2061 37.9697L62.207 37.9688L62.2324 37.8672C62.3877 37.3683 62.9024 37.0518 63.4219 37.1611H63.4209C63.9805 37.2733 64.3409 37.8163 64.2275 38.375L63.0449 44.2881C62.3668 47.6799 59.366 50.1406 55.9062 50.1406H36.4541C36.9418 52.5478 39.0843 54.328 41.5654 54.3281H57.2188C57.7881 54.3281 58.25 54.7901 58.25 55.3594C58.25 55.9287 57.7881 56.3906 57.2188 56.3906H41.5654C38.0662 56.3905 35.0468 53.857 34.4082 50.4443L34.3535 50.1113L31.5713 30.0723V30.0713C31.4288 29.042 30.5371 28.2656 29.498 28.2656H27.5312C26.9619 28.2656 26.5 27.8037 26.5 27.2344C26.5 26.6651 26.9619 26.2031 27.5312 26.2031H29.4971ZM57.2188 26.2031C57.7881 26.2031 58.25 26.6651 58.25 27.2344V32.4531H63.4688C64.0381 32.4531 64.5 32.9151 64.5 33.4844C64.5 34.0537 64.0381 34.5156 63.4688 34.5156H58.25V39.7344C58.25 40.3037 57.7881 40.7656 57.2188 40.7656C56.6494 40.7656 56.1875 40.3037 56.1875 39.7344V34.5156H50.9688C50.3994 34.5156 49.9375 34.0537 49.9375 33.4844C49.9375 32.9151 50.3994 32.4531 50.9688 32.4531H56.1875V27.2344C56.1875 26.6651 56.6494 26.2031 57.2188 26.2031Z"
                                        fill="white" stroke="white" stroke-width="0.5" />
                                    <defs>
                                        <filter id="filter0_f_3787_1792" x="0.5" y="0.203125" width="90"
                                            height="90" filterUnits="userSpaceOnUse"
                                            color-interpolation-filters="sRGB">
                                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                result="shape" />
                                            <feGaussianBlur stdDeviation="3.75"
                                                result="effect1_foregroundBlur_3787_1792" />
                                        </filter>
                                    </defs>
                                </svg>
                                {{-- <span class="button-icon button-icon-left"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span> --}}
                                {{-- <span
                                    class="text button-text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                <span class="button-icon button-icon-right"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span> --}}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="button-action mt-2">
                        <div class="addtocart-btn add_cart" id="add_cart"
                            data-product-variant-id="{{ $details->variants[0]->id }}" data-name='{{ $details->name }}'
                            data-slug='{{ $details->slug }}' data-brand-name='{{ $details->brand_name }}'
                            data-image='{{ dynamic_image($details->image, 220) }}' data-product-type='regular'
                            data-max='{{ $details->total_allowed_quantity }}'
                            data-step='{{ $details->quantity_step_size }}'
                            data-min='{{ $details->minimum_order_quantity }}'
                            data-stock-type='{{ $details->stock_type }}' data-store-id='{{ $details->store_id }}'
                            data-variant-price="{{ currentCurrencyPrice($details->variants[0]->special_price) }}">
                            <a class="add-to-cart-button-style-round" data-product-id="{{ $details->id }}">
                                <svg class="add-to-cart-icon" width="91" height="91" viewBox="0 0 91 91"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_f_3787_1792)">
                                        <circle cx="45.5" cy="45.2031" r="37.5" fill="#7EBB44" />
                                    </g>
                                    <circle cx="45.5" cy="45.2031" r="37.5" fill="#7EBB44" />
                                    <path
                                        d="M37.6875 57.4531C39.549 57.4531 41.0625 58.9666 41.0625 60.8281C41.0625 62.6896 39.549 64.2031 37.6875 64.2031C35.826 64.2031 34.3125 62.6896 34.3125 60.8281C34.3125 58.9666 35.826 57.4531 37.6875 57.4531ZM53.3125 57.4531C55.174 57.4531 56.6875 58.9666 56.6875 60.8281C56.6875 62.6896 55.174 64.2031 53.3125 64.2031C51.451 64.2031 49.9375 62.6896 49.9375 60.8281C49.9375 58.9666 51.451 57.4531 53.3125 57.4531ZM37.6875 59.5156C36.9632 59.5156 36.375 60.1051 36.375 60.8281C36.375 61.5512 36.9632 62.1406 37.6875 62.1406C38.4118 62.1406 39 61.5512 39 60.8281C39 60.1051 38.4118 59.5156 37.6875 59.5156ZM53.3125 59.5156C52.5882 59.5156 52 60.1051 52 60.8281C52 61.5512 52.5882 62.1406 53.3125 62.1406C54.0368 62.1406 54.625 61.5512 54.625 60.8281C54.625 60.1051 54.0368 59.5156 53.3125 59.5156ZM29.4971 26.2031L29.6895 26.208C31.6713 26.3003 33.3393 27.809 33.6133 29.7891L33.9834 32.4531H46.2812C46.8506 32.4531 47.3125 32.9151 47.3125 33.4844C47.3125 34.0537 46.8506 34.5156 46.2812 34.5156H34.2695L36.1533 48.0781H55.9062C58.384 48.0781 60.5374 46.3137 61.0234 43.8838L62.2061 37.9697L62.207 37.9688L62.2324 37.8672C62.3877 37.3683 62.9024 37.0518 63.4219 37.1611H63.4209C63.9805 37.2733 64.3409 37.8163 64.2275 38.375L63.0449 44.2881C62.3668 47.6799 59.366 50.1406 55.9062 50.1406H36.4541C36.9418 52.5478 39.0843 54.328 41.5654 54.3281H57.2188C57.7881 54.3281 58.25 54.7901 58.25 55.3594C58.25 55.9287 57.7881 56.3906 57.2188 56.3906H41.5654C38.0662 56.3905 35.0468 53.857 34.4082 50.4443L34.3535 50.1113L31.5713 30.0723V30.0713C31.4288 29.042 30.5371 28.2656 29.498 28.2656H27.5312C26.9619 28.2656 26.5 27.8037 26.5 27.2344C26.5 26.6651 26.9619 26.2031 27.5312 26.2031H29.4971ZM57.2188 26.2031C57.7881 26.2031 58.25 26.6651 58.25 27.2344V32.4531H63.4688C64.0381 32.4531 64.5 32.9151 64.5 33.4844C64.5 34.0537 64.0381 34.5156 63.4688 34.5156H58.25V39.7344C58.25 40.3037 57.7881 40.7656 57.2188 40.7656C56.6494 40.7656 56.1875 40.3037 56.1875 39.7344V34.5156H50.9688C50.3994 34.5156 49.9375 34.0537 49.9375 33.4844C49.9375 32.9151 50.3994 32.4531 50.9688 32.4531H56.1875V27.2344C56.1875 26.6651 56.6494 26.2031 57.2188 26.2031Z"
                                        fill="white" stroke="white" stroke-width="0.5" />
                                    <defs>
                                        <filter id="filter0_f_3787_1792" x="0.5" y="0.203125" width="90"
                                            height="90" filterUnits="userSpaceOnUse"
                                            color-interpolation-filters="sRGB">
                                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                result="shape" />
                                            <feGaussianBlur stdDeviation="3.75"
                                                result="effect1_foregroundBlur_3787_1792" />
                                        </filter>
                                    </defs>
                                </svg>
                                {{-- <span class="button-icon button-icon-left"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span> --}}
                                {{-- <span
                                    class="text button-text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                <span class="button-icon button-icon-right"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span> --}}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="product-details">
                {{-- <div class="product-price">
                    @php
                        if ($details->type == 'variable_product') {
                            $price = currentCurrencyPrice($details->min_max_price['max_price'], true);
                            $special_price =
                                $details->min_max_price['special_min_price'] &&
                                $details->min_max_price['special_min_price'] > 0
                                    ? currentCurrencyPrice($details->min_max_price['special_min_price'], true)
                                    : $price;
                            $dealer_price = currentCurrencyPrice($details->variants[0]->dealer_price, true);
                        } else {
                            $price = currentCurrencyPrice($details->variants[0]->price, true);
                            $special_price =
                                $details->variants[0]->special_price && $details->variants[0]->special_price > 0
                                    ? currentCurrencyPrice($details->variants[0]->special_price, true)
                                    : $price;
                            $dealer_price = currentCurrencyPrice($details->variants[0]->dealer_price, true);
                        }
                    @endphp

                    @if (Auth::check() && (Auth::user()->role->name === 'dealer' || Auth::user()->role->name === 'manager'))
                        <span class="price old-price">{{ $special_price !== $price ? $price : '' }}</span>
                        <span class="price fw-500"><span wire:model="price">{{ $special_price }}</span></span>
                        <span class="price end-price fw-500"><span
                                wire:model="dealer_price">{{ $dealer_price }}</span></span>
                    @else
                        <span class="price old-price">{{ $special_price !== $price ? $price : '' }}</span>
                        <span class="price end-price fw-500">{{ $price }}</span>
                    @endif
                </div> --}}
                <div class="product-price">
                    @php
                        if ($details->type == 'variable_product') {
                            $price = currentCurrencyPrice($details->min_max_price['max_price'], true);
                            $special_price =
                                $details->min_max_price['special_min_price'] &&
                                $details->min_max_price['special_min_price'] > 0
                                    ? currentCurrencyPrice($details->min_max_price['special_min_price'], true)
                                    : $price;
                            $discount =
                                $details->min_max_price['special_min_price'] &&
                                $details->min_max_price['special_min_price'] > 0
                                    ? (($details->min_max_price['max_price'] -
                                            $details->min_max_price['special_min_price']) /
                                            $details->min_max_price['max_price']) *
                                        100
                                    : $price;
                        } else {
                            $price = currentCurrencyPrice($details->variants[0]->price, true);
                            $special_price =
                                $details->variants[0]->special_price && $details->variants[0]->special_price > 0
                                    ? currentCurrencyPrice($details->variants[0]->special_price, true)
                                    : $price;
                            $discount =
                                $details->variants[0]->special_price && $details->variants[0]->special_price > 0
                                    ? (($details->variants[0]->price - $details->variants[0]->special_price) /
                                            $details->variants[0]->price) *
                                        100
                                    : $price;
                        }
                    @endphp
                    <div class="with-discount">
                        <span class="old-price">{{ $special_price !== $price ? $price : ' ' }}</span>
                        <span class="discount">{{ $special_price !== $price ? '-' . $discount . '%' : '' }}</span>
                    </div>
                    <span class="price end-price fw-500"><span wire:model="price">{{ $special_price }}</span></span>
                </div>

                <a wire:navigate href="{{ customUrl('products/?brand=' . $details->brand_slug) }}"
                    class="slider-link product-vendor text-uppercase"
                    data-link="{{ customUrl('products/?brand=' . $details->brand_slug) }}">{!! $details->brand_name !!}</a>
                <div class="product-name text-capitalize">
                    <a wire:navigate href="{{ customUrl('products/' . $details->slug) }}"
                        class="slider-link text-ellipsis" data-link="{{ customUrl('products/' . $details->slug) }}"
                        title="{!! $details->name !!}">{!! $details->name !!}</a>
                </div>

                <div>
                    {{-- <p class="sort-desc hidden">{!! $details->short_description !!}</p> --}}
                    <a wire:navigate href="{{ customUrl('categories/' . $details->category_slug . '/products') }}"
                        data-link="{{ customUrl('categories/' . $details->category_slug . '/products') }}"
                        class="slider-link text-ellipsis hidden text-secondary"
                        title="{!! $details->category_name !!}"><ion-icon name="layers-outline"
                            class="custom-icon fs-6 me-1"></ion-icon>{!! $details->category_name !!}
                    </a>
                </div>



            </div>
            <div class="product-review-fixed">
                <div class="product-review-star">

                    <svg width="22" height="20" viewBox="0 0 22 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.1416 1.31849C10.5862 0.700564 11.5058 0.700565 11.9504 1.3185L14.9348 5.46639C15.0823 5.67132 15.295 5.8201 15.538 5.88835L20.519 7.28696C21.2895 7.5033 21.5874 8.4372 21.0846 9.05974L17.9656 12.9209C17.7986 13.1277 17.711 13.3874 17.7187 13.6532L17.8607 18.5735C17.8831 19.3495 17.1249 19.91 16.3896 19.6609L11.4035 17.972C11.1717 17.8935 10.9204 17.8935 10.6886 17.972L5.70248 19.6609C4.96718 19.91 4.20894 19.3495 4.23135 18.5735L4.37342 13.6532C4.38109 13.3874 4.2935 13.1277 4.12645 12.9209L1.00752 9.05974C0.504655 8.4372 0.802574 7.5033 1.57304 7.28696L6.55404 5.88835C6.79711 5.8201 7.00979 5.67132 7.15724 5.46639L10.1416 1.31849Z"
                            fill="white" />
                    </svg>
                </div>
                <div class="product-review-value">
                    {{ $details->rating }}
                </div>
            </div>
        </div>
    @else
        <div class="product-box">
            <div class="product-image m-0">
                <a wire:navigate href="{{ customUrl('combo-products/' . $details->slug) }}"
                    class="all-product-img product-img rounded-3 slider-link"
                    data-link="{{ customUrl('combo-products/' . $details->slug) }}">
                    <img class="blur-up lazyload" src="{{ dynamic_image($details->image, 400) }}"
                        alt="{{ $details->name }}" title="{{ $details->name }}" width="625" height="808" />
                </a>
                <div class="button-set style1">
                    <a href="#quickview-modal" class="btn-icon quickview quick-view-modal" data-bs-toggle="modal"
                        data-bs-target="#quickview_modal" data-product-id="{{ $details->id }}"
                        data-product-type="{{ $details->type }}">
                        <span class="icon-wrap d-flex-justify-center h-100 w-100" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="Quick View"><ion-icon name="search-outline"
                                class="fs-6"></ion-icon><span
                                class="text">{{ labels('front_messages.quick_view', 'Quick View') }}</span>
                    </a>
                    <a class="btn-icon wishlist card_fav_btn {{ $details->is_favorite == 1 ? 'remove-favorite' : 'add-favorite' }}"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Add To Wishlist"
                        data-product-id="{{ $details->id }}" data-product-type="combo"><ion-icon
                            name="{{ $details->is_favorite == 1 ? 'heart' : 'heart-outline' }}"
                            class="{{ $details->is_favorite == 1 ? 'text-danger' : '' }} fs-6"></ion-icon><span
                            class="text">{{ labels('front_messages.add_to_Wishlist', 'Add To Wishlist') }}</span></a>

                    <a class="btn-icon compare add-compare" data-product-id="{{ $details->id }}"
                        data-product-type="combo" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Add to Compare"><ion-icon name="shuffle-outline" class="fs-6"></ion-icon><span
                            class="text">{{ labels('front_messages.add_to_compare', 'Add to Compare') }}</span></a>
                </div>

                @if ($details->type == 'variable_product')
                    <div class="button-action mt-2">
                        <div class="addtocart-btn">

                            <a href="#quickview-modal"
                                class="button-style-round btn  btn-secondary btn-md quickview quick-view-modal p-2"
                                data-bs-toggle="modal" data-bs-target="#quickview_modal"
                                data-product-id="{{ $details->id }}" data-product-variant-id=''>
                                <svg width="18" height="18" viewBox="0 0 52 52" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.583 42.167C18.157 42.167 20.2498 44.2591 20.25 46.833C20.25 49.4071 18.1571 51.5 15.583 51.5C13.0091 51.4998 10.917 49.407 10.917 46.833C10.9172 44.2592 13.0092 42.1672 15.583 42.167ZM36.417 42.167C38.9908 42.1672 41.0828 44.2592 41.083 46.833C41.083 49.407 38.9909 51.4998 36.417 51.5C33.8429 51.5 31.75 49.4071 31.75 46.833C31.7502 44.2591 33.843 42.167 36.417 42.167ZM15.583 45.25C14.7097 45.2502 14.0002 45.9612 14 46.833C14 47.705 14.7096 48.4168 15.583 48.417C16.4565 48.417 17.167 47.7051 17.167 46.833C17.1668 45.9611 16.4564 45.25 15.583 45.25ZM36.417 45.25C35.5436 45.25 34.8332 45.9611 34.833 46.833C34.833 47.7051 35.5435 48.417 36.417 48.417C37.2904 48.4168 38 47.705 38 46.833C37.9998 45.9612 37.2903 45.2502 36.417 45.25ZM4.66211 0.5C7.49928 0.5 9.92788 2.6174 10.3164 5.4248L10.79 8.83301H27.042C27.893 8.83318 28.583 9.52397 28.583 10.375C28.583 11.226 27.893 11.9168 27.042 11.917H11.2178L13.6836 29.667H39.875C43.0993 29.667 45.9028 27.37 46.5352 24.208L48.1113 16.3232L48.1123 16.3213L48.1514 16.1689C48.3827 15.4261 49.1469 14.954 49.9238 15.1143L49.9248 15.1133C50.7626 15.2797 51.3035 16.0924 51.1338 16.9287L49.5566 24.8125C48.637 29.4129 44.5675 32.75 39.875 32.75H14.1455C14.856 35.7808 17.5947 37.9999 20.7539 38H41.625C42.4761 38 43.167 38.6908 43.167 39.542C43.1668 40.393 42.476 41.083 41.625 41.083H20.7539C16.0075 41.0829 11.9128 37.6474 11.0469 33.0186L10.9736 32.5664L7.2627 5.84766C7.08439 4.5576 5.96718 3.58324 4.66504 3.58301H2.04199C1.19096 3.58301 0.500176 2.89298 0.5 2.04199C0.5 1.19085 1.19085 0.5 2.04199 0.5H4.66211ZM41.625 0.5C42.4761 0.5 43.167 1.19085 43.167 2.04199V8.83301H49.958C50.8092 8.83301 51.5 9.52386 51.5 10.375C51.5 11.2261 50.8092 11.917 49.958 11.917H43.167V18.708C43.167 19.5591 42.4761 20.25 41.625 20.25C40.7739 20.25 40.083 19.5592 40.083 18.708V11.917H33.292C32.4409 11.917 31.75 11.2261 31.75 10.375C31.75 9.52386 32.4409 8.83301 33.292 8.83301H40.083V2.04199C40.083 1.19085 40.7739 0.5 41.625 0.5Z"
                                        fill="white" stroke="white" />
                                </svg>
                            </a>


                            {{-- <a href="#quickview-modal" class="button-style btn btn-md quickview quick-view-modal p-2"
                                data-bs-toggle="modal" data-bs-target="#quickview_modal"
                                data-product-id="{{ $details->id }}" data-product-variant-id=''>
                                <span class="button-icon button-icon-left"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                                <span
                                    class="text button-text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                <span class="button-icon button-icon-right"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                            </a> --}}
                        </div>
                    </div>
                @else
                    <div class="button-action mt-2">

                        <div class="addtocart-btn add_cart" id="add_cart"
                            data-product-variant-id="{{ $details->id }}" data-name='{{ $details->name }}'
                            data-slug='{{ $details->slug }}' data-image='{{ dynamic_image($details->image, 220) }}'
                            data-product-type='combo' data-max='{{ $details->total_allowed_quantity }}'
                            data-step='{{ $details->quantity_step_size }}'
                            data-min='{{ $details->minimum_order_quantity }}'
                            data-stock-type='{{ $details->stock_type }}' data-store-id='{{ $details->store_id }}'
                            data-variant-price="{{ currentCurrencyPrice($details->special_price) }}">
                            <a class="btn btn-secondary btn-md p-2 button-style-round"
                                data-product-id="{{ $details->id }}">
                                <svg width="18" height="18" viewBox="0 0 52 52" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.583 42.167C18.157 42.167 20.2498 44.2591 20.25 46.833C20.25 49.4071 18.1571 51.5 15.583 51.5C13.0091 51.4998 10.917 49.407 10.917 46.833C10.9172 44.2592 13.0092 42.1672 15.583 42.167ZM36.417 42.167C38.9908 42.1672 41.0828 44.2592 41.083 46.833C41.083 49.407 38.9909 51.4998 36.417 51.5C33.8429 51.5 31.75 49.4071 31.75 46.833C31.7502 44.2591 33.843 42.167 36.417 42.167ZM15.583 45.25C14.7097 45.2502 14.0002 45.9612 14 46.833C14 47.705 14.7096 48.4168 15.583 48.417C16.4565 48.417 17.167 47.7051 17.167 46.833C17.1668 45.9611 16.4564 45.25 15.583 45.25ZM36.417 45.25C35.5436 45.25 34.8332 45.9611 34.833 46.833C34.833 47.7051 35.5435 48.417 36.417 48.417C37.2904 48.4168 38 47.705 38 46.833C37.9998 45.9612 37.2903 45.2502 36.417 45.25ZM4.66211 0.5C7.49928 0.5 9.92788 2.6174 10.3164 5.4248L10.79 8.83301H27.042C27.893 8.83318 28.583 9.52397 28.583 10.375C28.583 11.226 27.893 11.9168 27.042 11.917H11.2178L13.6836 29.667H39.875C43.0993 29.667 45.9028 27.37 46.5352 24.208L48.1113 16.3232L48.1123 16.3213L48.1514 16.1689C48.3827 15.4261 49.1469 14.954 49.9238 15.1143L49.9248 15.1133C50.7626 15.2797 51.3035 16.0924 51.1338 16.9287L49.5566 24.8125C48.637 29.4129 44.5675 32.75 39.875 32.75H14.1455C14.856 35.7808 17.5947 37.9999 20.7539 38H41.625C42.4761 38 43.167 38.6908 43.167 39.542C43.1668 40.393 42.476 41.083 41.625 41.083H20.7539C16.0075 41.0829 11.9128 37.6474 11.0469 33.0186L10.9736 32.5664L7.2627 5.84766C7.08439 4.5576 5.96718 3.58324 4.66504 3.58301H2.04199C1.19096 3.58301 0.500176 2.89298 0.5 2.04199C0.5 1.19085 1.19085 0.5 2.04199 0.5H4.66211ZM41.625 0.5C42.4761 0.5 43.167 1.19085 43.167 2.04199V8.83301H49.958C50.8092 8.83301 51.5 9.52386 51.5 10.375C51.5 11.2261 50.8092 11.917 49.958 11.917H43.167V18.708C43.167 19.5591 42.4761 20.25 41.625 20.25C40.7739 20.25 40.083 19.5592 40.083 18.708V11.917H33.292C32.4409 11.917 31.75 11.2261 31.75 10.375C31.75 9.52386 32.4409 8.83301 33.292 8.83301H40.083V2.04199C40.083 1.19085 40.7739 0.5 41.625 0.5Z"
                                        fill="white" stroke="white" />
                                </svg>

                            </a>
                        </div>
                        {{-- <div class="addtocart-btn add_cart" id="add_cart"
                            data-product-variant-id="{{ $details->id }}" data-name='{{ $details->name }}'
                            data-slug='{{ $details->slug }}' data-image='{{ dynamic_image($details->image, 220) }}'
                            data-product-type='combo' data-max='{{ $details->total_allowed_quantity }}'
                            data-step='{{ $details->quantity_step_size }}'
                            data-min='{{ $details->minimum_order_quantity }}'
                            data-stock-type='{{ $details->stock_type }}' data-store-id='{{ $details->store_id }}'
                            data-variant-price="{{ currentCurrencyPrice($details->special_price) }}">
                            <a class="btn btn-md p-2 button-style" data-product-id="{{ $details->id }}">
                                <span class="button-icon button-icon-left"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                                <span
                                    class="text button-text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                <span class="button-icon button-icon-right"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                            </a>
                        </div> --}}
                    </div>
                @endif

            </div>
            <div class="product-details">

                <div class="product-price">
                    @php
                        $price = currentCurrencyPrice($details->price, true);
                        $special_price =
                            $details->special_price && $details->special_price > 0
                                ? currentCurrencyPrice($details->special_price, true)
                                : $price;
                        $discount =
                            $details->special_price && $details->special_price > 0
                                ? (($details->price - $details->special_price) / $details->price) * 100
                                : $price;
                    @endphp
                    <div class="with-discount">
                        <span class="old-price">{{ $special_price !== $price ? $price : ' ' }}</span>
                        <span
                            class="discount">{{ $special_price !== $price ? '-' . round($discount, 2) . '%' : '' }}</span>
                    </div>
                    <span class="price end-price fw-500"><span wire:model="price">{{ $special_price }}</span></span>
                    {{-- <span
                        class=" old-price">{{ $details->special_price && $details->special_price > 0 ? $price : '' }}</span>
                    <span class="price fw-500">{{ $special_price }}</span> --}}
                </div>

                <div class="product-name text-capitalize">
                    <a wire:navigate href="{{ customUrl('combo-products/' . $details->slug) }}"
                        class="slider-link text-ellipsis"
                        data-link="{{ customUrl('combo-products/' . $details->slug) }}"
                        title="{!! $details->name !!}">{!! $details->name !!}</a>
                </div>
            </div>
            <div class="product-review-fixed">
                <div class="product-review-star">

                    <svg width="19" height="18" viewBox="0 0 57 54" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M26.2621 1.54295C27.4543 -0.19674 30.0218 -0.196739 31.2141 1.54295L39.2499 13.2682C39.6402 13.8377 40.2149 14.2553 40.8772 14.4505L54.5118 18.4697C56.5347 19.0661 57.3281 21.5078 56.042 23.1793L47.3738 34.4452C46.9528 34.9924 46.7333 35.668 46.7523 36.3582L47.1431 50.5675C47.2011 52.6757 45.124 54.1848 43.1368 53.4782L29.7438 48.7156C29.0932 48.4842 28.3829 48.4842 27.7324 48.7156L14.3393 53.4782C12.3521 54.1848 10.2751 52.6757 10.333 50.5675L10.7238 36.3582C10.7428 35.668 10.5233 34.9924 10.1023 34.4452L1.4341 23.1793C0.147994 21.5078 0.941369 19.0661 2.96434 18.4697L16.599 14.4505C17.2612 14.2553 17.8359 13.8377 18.2262 13.2682L26.2621 1.54295Z"
                            fill="white" />
                    </svg>
                </div>
                <div class="product-review-value">
                    {{ $details->rating }}
                </div>
            </div>
        </div>
    @endif
</div>
