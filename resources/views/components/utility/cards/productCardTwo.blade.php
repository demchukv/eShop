@props(['details'])
{{-- @dd($details) --}}
<div class="item col-item">
    @if ($details->type != 'combo-product')
        <div class="product-box">
            <div class="product-image m-0">
                @php
                    $main_img = dynamic_image($details->image, 650);
                    $other_images = dynamic_image($details->other_images[0] ?? $details->image, 650);
                @endphp
                <a wire:navigate href="{{ customUrl('products/' . $details->slug) }}"
                    class="img-box-h300px product-img slider-link"
                    data-link="{{ customUrl('products/' . $details->slug) }}">
                    <img class="primary blur-up lazyload" data-src="{{ $main_img }}" src="{{ $main_img }}"
                        alt="Product" title="{{ $details->name }}" width="625" height="625" />
                    <img class="hover blur-up lazyload" data-src="{{ $other_images ?? $main_img }}"
                        src="{{ $other_images ?? $main_img }}" alt="Product" title="{{ $details->name }}"
                        width="625" height="625" />
                    {{-- @endif --}}

                </a>
                <div class="button-set style1">
                    <a href="#quickview-modal" class="btn-icon quickview quick-view-modal" data-bs-toggle="modal" data-bs-target="#quickview_modal" data-product-id="{{ $details->id }}">
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
            </div>
            <div class="product-details text-left">
                <a wire:navigate href="{{ customUrl('products/?brand=' . $details->brand_slug) }}"
                    class="slider-link product-vendor text-uppercase"
                    data-link="{{ customUrl('products/?brand=' . $details->brand_slug) }}">{!! $details->brand_name !!}</a>
                <div class="product-name text-capitalize">
                    <a wire:navigate href="{{ customUrl('products/' . $details->slug) }}"
                        class="slider-link text-ellipsis" data-link="{{ customUrl('products/' . $details->slug) }}"
                        title="{!! $details->name !!}">{!! $details->name !!}</a>
                </div>
                <div class="product-price">
                    @php
                         $price = currentCurrencyPrice($details->min_max_price['max_price'], true);
                            $special_price =
                                $details->min_max_price['special_min_price'] &&
                                $details->min_max_price['special_min_price'] > 0
                                    ? currentCurrencyPrice($details->min_max_price['special_min_price'], true)
                                    : $price;
                    @endphp
                     <span class="price old-price">{{ $special_price !== $price ? $price : '' }}</span>
                     <span class="price fw-500"><span wire:model="price">{{ $special_price }}</span></span>
                </div>
                <input id="input-3-ltr-star-md" name="input-3-ltr-star-md"
                    class="kv-ltr-theme-svg-star rating-loading d-none" value="{{ $details->rating }}" dir="ltr"
                    data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                @if ($details->type == 'variable_product')
                    <div class="button-action pt-2">
                        <div class="addtocart-btn">
                            <a href="#quickview-modal"
                                class="add_cart button-style btn btn-md quickview quick-view-modal p-2"
                                data-bs-toggle="modal" data-bs-target="#quickview_modal"
                                data-product-id="{{ $details->id }}" data-product-variant-id=''>
                                <span class="button-icon button-icon-left"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                                <span
                                    class="text button-text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                <span class="button-icon button-icon-right"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="button-action pt-2">
                        <div class="addtocart-btn add_cart" id="add_cart"
                            data-product-variant-id="{{ $details->variants[0]->id }}" data-name='{{ $details->name }}'
                            data-slug='{{ $details->slug }}' data-brand-name='{{ $details->brand_name }}'
                            data-image='{{ $main_img }}' data-product-type='regular'
                            data-max='{{ $details->total_allowed_quantity }}'
                            data-step='{{ $details->quantity_step_size }}'
                            data-min='{{ $details->minimum_order_quantity }}'
                            data-stock-type='{{ $details->stock_type }}'
                            data-store-id='{{ $details->store_id }}'
                            data-variant-price="{{ currentCurrencyPrice($details->variants[0]->special_price) }}">
                            <a class="btn btn-md p-2 button-style" data-product-id="{{ $details->id }}">
                                <span class="button-icon button-icon-left"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                                <span
                                    class="text button-text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                <span class="button-icon button-icon-right"><ion-icon
                                        name="bag-handle-outline"></ion-icon></span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="product-box">
            <div class="product-image m-0">
                <a wire:navigate href="{{ customUrl('combo-products/' . $details->slug) }}"
                    class="img-box-h300px product-img slider-link"
                    data-link="{{ customUrl('combo-products/' . $details->slug) }}">
                    @php
                        $main_img = dynamic_image($details->image, 450);
                        $other_images = dynamic_image($details->other_images[0] ?? $details->image , 450);
                    @endphp
                    <img class="primary blur-up lazyload" data-src="{{ $main_img }}" src="{{ $main_img }}"
                        alt="Product" title="{{ $details->name }}" width="625" height="625" />
                    {{-- @if (count($details->other_images) > 0) --}}
                    <img class="hover blur-up lazyload" data-src="{{ $other_images ?? $main_img }}"
                        src="{{ $other_images ?? $main_img }}" alt="Product" title="{{ $details->name }}"
                        width="625" height="625" />
                    {{-- @endif --}}

                </a>
                <div class="button-set style1">
                    <a href="#quickview-modal" class="btn-icon quickview quick-view-modal" data-bs-toggle="modal"
                        data-bs-target="#quickview_modal" data-product-id="{{ $details->id }}"  data-product-type="combo-product">
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
                        data-product-type="combo" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Add to Compare"><ion-icon name="shuffle-outline" class="fs-6"></ion-icon><span
                            class="text">{{ labels('front_messages.add_to_compare', 'Add to Compare') }}</span></a>
                </div>
            </div>
            <div class="product-details text-left">
                <div class="product-name text-capitalize">
                    <a wire:navigate href="{{ customUrl('combo-products/' . $details->slug) }}"
                        class="slider-link text-ellipsis"
                        data-link="{{ customUrl('combo-products/' . $details->slug) }}"
                        title="{!! $details->name !!}">{!! $details->name !!}</a>
                </div>
                <div class="product-price">
                    @php
                        $price = currentCurrencyPrice($details->price, true);
                        $special_price = currentCurrencyPrice($details->special_price, true);
                    @endphp
                    <span class="price old-price">{{ $price }}</span><span
                        class="price fw-500">{{ $special_price }}</span>
                </div>
                <input id="input-3-ltr-star-md" name="input-3-ltr-star-md"
                    class="kv-ltr-theme-svg-star rating-loading d-none" value="{{ $details->rating }}"
                    dir="ltr" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                <div class="button-action pt-2">
                    <div class="addtocart-btn add_cart" id="add_cart" data-product-variant-id="{{ $details->id }}"
                        data-name='{{ $details->name }}' data-slug='{{ $details->slug }}' data-brand-name=''
                        data-image='{{ $main_img }}' data-product-type='regular'
                        data-max='{{ $details->total_allowed_quantity }}'
                        data-step='{{ $details->quantity_step_size }}'
                        data-min='{{ $details->minimum_order_quantity }}'
                        data-stock-type='{{ $details->stock_type }}'
                        data-store-id='{{ $details->store_id }}'
                        data-variant-price="{{ currentCurrencyPrice($details->special_price) }}">
                        <a class="btn btn-md p-2 button-style" data-product-id="{{ $details->id }}">
                            <span class="button-icon button-icon-left"><ion-icon
                                    name="bag-handle-outline"></ion-icon></span>
                            <span
                                class="text button-text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                            <span class="button-icon button-icon-right"><ion-icon
                                    name="bag-handle-outline"></ion-icon></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
