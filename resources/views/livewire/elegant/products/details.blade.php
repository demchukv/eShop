<div id="page-content" wire:ignore>
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        {{-- @dd($product_details) --}}

        {{-- @php
            dd($product_details);
        @endphp --}}
        <div class="product-single">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 product-layout-img mb-4 mb-md-0">
                    {{-- <div class="product-sticky-style"> --}}
                    <div class="product-details-img product-thumb-left-style d-flex justify-content-center">
                        <div class="product-thumb thumb-left">
                            <div id="gallery" class="product-thumb-vertical h-100">
                                @php
                                    $main_image = dynamic_image($product_details->image, 600);
                                    $main_image_zoom = dynamic_image($product_details->image, 800);
                                @endphp
                                <a data-image="{{ $main_image }}" data-zoom-image="{{ $main_image_zoom }}"
                                    class="slick-slide slick-cloned active">
                                    <img class="blur-up lazyload rounded-2" data-src="{{ $main_image }}"
                                        src="{{ $main_image }}" alt="product" width="625" height="808" />
                                </a>
                                @foreach ($product_details->other_images as $images)
                                    @php
                                        $other_image = dynamic_image($images, 600);
                                        $other_image_zoom = dynamic_image($images, 800);
                                    @endphp
                                    <a data-image="{{ $other_image }}" data-zoom-image="{{ $other_image_zoom }}"
                                        class="slick-slide slick-cloned active">
                                        <img class="blur-up lazyload rounded-2" data-src="{{ $other_image }}"
                                            src="{{ $other_image }}" alt="product" width="625" height="808" />
                                    </a>
                                @endforeach
                                @foreach ($product_details->variants as $variants)
                                    @foreach ($variants->images as $images)
                                        @php
                                            $other_image = dynamic_image($images, 600);
                                            $other_image_zoom = dynamic_image($images, 800);
                                        @endphp
                                        <a data-image="{{ $other_image }}" data-zoom-image="{{ $other_image_zoom }}"
                                            class="slick-slide slick-cloned active">
                                            <img class="blur-up lazyload rounded-0" data-src="{{ $other_image }}"
                                                src="{{ $other_image }}" alt="product" width="625"
                                                height="808" />
                                        </a>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <div class="zoompro-wrap product-zoom-right rounded-3">
                            <div class="zoompro-span"><img id="zoompro" class="zoompro rounded-0"
                                    src="{{ $main_image }}" data-zoom-image="{{ $main_image_zoom }}"
                                    alt="product" /></div>
                        </div>
                    </div>
                    {{-- @dd($system_settings) --}}
                    {{-- <div class="social-sharing d-flex-center justify-content-center mt-3 mt-md-4 lh-lg">
                            <span class="sharing-lbl fw-600">{{ labels('front_messages.share', 'Share') }} :</span>
                            <div class="shareon">
                                <a class="facebook"
                                    data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                <a class="telegram"
                                    data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                <a class="twitter"
                                    data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                <a class="whatsapp"
                                    data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                <a class="email"
                                    data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                <a class="copy-url"></a>
                            </div>
                        </div> --}}
                    {{-- </div> --}}

                    <div>
                        <h3 class="mb-1 pb-0">Description</h3>
                        <div class="mb-10px text-muted with-background">{{ $product_details->short_description }}</div>
                        <!--Description-->
                        @if ($product_details->description != '')
                            <h3 class="mb-1 pb-0">
                                {{ labels('front_messages.description', 'Detailed information') }}</h3>
                            <div class="mb-10px text-muted with-background">
                                <div class="product-description">
                                    <div class="row">
                                        <div class="col-12 product-description">
                                            {!! $product_details->description !!}
                                            <div class="mt-3">
                                                {!! $product_details->extra_description !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End Description-->
                        @endif
                        @if ($product_details->attributes != [])
                            <!--Additional Information-->
                            <h3 class="mb-1 pb-0">
                                {{ labels('front_messages.additional_information', 'Additional Information') }}
                            </h3>
                            <div class="mb-10px text-muted with-background">
                                <div class="product-description">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-4 mb-md-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle table-part mb-0">
                                                    @foreach ($product_details->attributes as $attributes)
                                                        <tr>
                                                            {{-- @dd($attributes) --}}
                                                            <th>{{ $attributes['name'] }}</th>
                                                            <td>{{ $attributes['value'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End Additional Information-->
                        @endif

                        <livewire:pages.customer-ratings :product_id="$product_id" :product_details="$product_details" />

                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-12 product-layout-info">
                    <div class="d-flex mb-1">
                        <div class="flex-fill">
                            <p class="infolinks d-flex-center justify-content-start mb-2">
                                <a class="cursor-pointer text-link remove-favorite rem-fav-btn text-danger {{ $product_details->is_favorite == 0 ? 'd-none' : 'd-flex' }}"
                                    data-product-id="{{ $product_details->id }}"><ion-icon name="heart"
                                        class="fs-5 me-2"></ion-icon><span>{{ labels('front_messages.remove_from_wishlist', 'Remove from Wishlist') }}</span></a>
                                <a class="cursor-pointer text-link add-favorite {{ $product_details->is_favorite == 0 ? 'd-flex' : 'd-none' }}"
                                    data-product-id="{{ $product_details->id }}"><ion-icon name="heart-outline"
                                        class="fs-5 me-2"></ion-icon><span>{{ labels('front_messages.add_to_wishlist', 'Add to Wishlist') }}</span></a>
                                <a class="text-link add-compare" data-product-id="{{ $product_details->id }}"
                                    data-product-variant-id=""><ion-icon name="repeat-outline"
                                        class="fs-5 me-2"></ion-icon>
                                    <span>{{ labels('front_messages.add_to_compare', 'Add to Compare') }}</span></a>
                            </p>
                        </div>
                        <div class="d-flex justify-content-end">
                            @php
                                $category = fetchDetails('categories', ['id' => $product_details->category_id], 'slug');
                            @endphp
                            <a href="{{ customUrl('categories/' . $category[0]->slug . '/products') }}"
                                class="product-type fs-6 mb-10px" title="{!! $product_details->category_name !!}"><ion-icon
                                    name="layers-outline"
                                    class="custom-icon fs-6 me-1"></ion-icon>{!! $product_details->category_name !!}
                            </a>
                        </div>
                    </div>

                    <div class="d-flex mb-1">
                        <div class="flex-fill product-detail-action">
                            ✓ Free shipping
                        </div>
                        <div class="flex-fill product-detail-dealer">
                            $5 Dealer saving
                        </div>
                    </div>

                    <div class="product-single-meta">
                        <h2 class="product-main-title mb-2 text-capitalize">{{ $product_details->name }}</h2>

                        <div class="d-flex justify-content-between align-items-center">
                            @if (!empty($product_details->made_in))
                                <p class="product-sku pb-1 mb-10px">
                                    {{ labels('front_messages.made_in', 'Made In') }}:<span
                                        class="text fw-500">{{ $product_details->made_in }}</span></p>
                            @endif

                            <div class="product-review d-flex justify-content-end align-items-center mb-2">
                                <span class="product-review-number">{{ $product_details->rating }}</span>
                                <input id="input-3-ltr-star-md" name="input-3-ltr-star-md"
                                    class="kv-ltr-theme-svg-star rating-loading d-none"
                                    value="{{ $product_details->rating }}" dir="ltr" data-size="xs"
                                    data-show-clear="false" data-show-caption="false" readonly>
                            </div>
                        </div>

                        <div class="product-price ">
                            @if ($product_details->type != 'variable_product')
                                @php
                                    $price = currentCurrencyPrice($product_details->variants[0]->price, true);
                                    $dealer_price = currentCurrencyPrice(
                                        $product_details->variants[0]->dealer_price,
                                        true,
                                    );
                                    $diff_price = currentCurrencyPrice(
                                        calculateDealerComission($product_details),
                                        true,
                                    );
                                    $special_price =
                                        $product_details->variants[0]->special_price &&
                                        $product_details->variants[0]->special_price > 0
                                            ? currentCurrencyPrice($product_details->variants[0]->special_price, true)
                                            : $price;
                                @endphp
                                <span class="old-price">{{ $special_price !== $price ? $price : '' }}</span>
                                <span class="product_price" id="price">{{ $special_price }}</span>
                                {{-- @if (Auth::check() && (Auth::user()->role->name === 'dealer' || Auth::user()->role->name === 'manager') && $referral_link)
                                    <span class="price fw-500 text-success px-3 fs-2"
                                        id="dealer_price">{{ $dealer_price }}</span>
                                @endif --}}
                            @else
                                @php
                                    $max_dealer_price = 0;
                                    $min_dealer_price = 100000000;
                                    foreach ($product_details->variants as $variant) {
                                        $max_dealer_price =
                                            $variant->dealer_price > $max_dealer_price
                                                ? $variant->dealer_price
                                                : $max_dealer_price;
                                        $min_dealer_price =
                                            $variant->dealer_price < $min_dealer_price && $variant->dealer_price > 0
                                                ? $variant->dealer_price
                                                : $min_dealer_price;
                                    }
                                    $dealer_price = '';
                                    $diff_price = 0;
                                    $max_price = currentCurrencyPrice(
                                        $product_details->min_max_price['max_price'],
                                        true,
                                    );
                                    $special_min_price =
                                        $product_details->min_max_price['special_min_price'] &&
                                        $product_details->min_max_price['special_min_price'] > 0
                                            ? currentCurrencyPrice(
                                                $product_details->min_max_price['special_min_price'],
                                                true,
                                            )
                                            : $max_price;
                                    $diff_price = currentCurrencyPrice(
                                        $product_details->min_max_price['max_price'] - $min_dealer_price,
                                        true,
                                    );
                                @endphp
                                <span class="price old-price" id="special_price"></span>
                                <span class="price product_price" id="price">{{ $max_price }} -
                                    {{ $special_min_price }}</span>
                                {{-- @if (Auth::check() && (Auth::user()->role->name === 'dealer' || Auth::user()->role->name === 'manager') && $referral_link)
                                    <span class="price fw-500 text-success px-3 fs-2"
                                        id="dealer_price">{{ currentCurrencyPrice($min_dealer_price, true) }} -
                                        {{ currentCurrencyPrice($max_dealer_price, true) }}</span>
                                @endif --}}
                            @endif
                        </div>
                        <!-- Реферальне посилання для дилерів -->
                        {{-- @auth
                            @if (Auth::check() && (Auth::user()->role->name === 'dealer' || Auth::user()->role->name === 'manager') && $referral_link)
                                <div class="alert alert-warning mb-2 " role="alert">
                                    <div>You can earn up to: <span id="diff_price">{{ $diff_price }}</span></div>
                                    <div class="referral-link d-flex gap-2">
                                        <label
                                            class="fw-400">{{ labels('front_messages.referral_link', 'Referral Link') }}:
                                            <span>{{ $referral_link }}</span></label>
                                        <livewire:components.copy-button :text="$referral_link" />
                                    </div>
                                </div>
                            @endif
                        @endauth --}}

                        {{-- @dd($product_details); --}}

                        {{-- <hr class="light-hr" /> --}}


                    </div>
                    {{-- <hr class="light-hr" /> --}}
                    @if (!empty($product_details->attributes))

                        <div class="product-swatches-option">
                            @foreach ($product_details->attributes as $attributes)
                                @php
                                    $attribute_ids = explode(',', $attributes['ids']);
                                    $attribute_values = explode(',', $attributes['value']);
                                @endphp
                                <div class="product-item swatches-size w-100 mb-2 swatch-1 option2"
                                    data-option-index="1">
                                    <label
                                        class="label d-flex align-items-center swatch-label">{{ $attributes['name'] }}</label>
                                    <ul class="variants-size size-swatches d-flex-center pt-1 clearfix">
                                        @foreach ($attribute_values as $key => $val)
                                            <li class="swatch x-large available py-1 px-2 toggleInput"
                                                onclick="toggleInput(this)">
                                                <input type="radio" class="swatchLbl attributes d-none"
                                                    data-bs-toggle="tooltip" value="{{ $attribute_ids[$key] }}"
                                                    data-bs-placement="top" title="{{ $val }}"
                                                    id="variant-{{ $attribute_ids[$key] }}">{{ $val }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                            {{-- @dd($product_details->attributes); --}}
                        </div>
                    @endempty

                    @foreach ($product_details->variants as $variant)
                        <input type="hidden" class="variants" name="variants_ids" data-image-index=""
                            data-name="" value="{{ $variant->attribute_value_ids }}"
                            data-id="{{ $variant->id }}"
                            data-price="{{ currentCurrencyPrice($variant->price) }}"
                            data-special_price="{{ currentCurrencyPrice($variant->special_price) }}"
                            data-dealer_price="{{ currentCurrencyPrice($variant->dealer_price) }}" />
                    @endforeach

                    <div class="product-action w-100 d-flex-wrap my-3 my-md-4">
                        <div class="product-form-quantity d-flex-center">
                            <div class="qtyField">
                                <button class="qtyBtn minus" href="#;">-</button>
                                <input type="number" name="quantity" value="1"
                                    class="product-form-input qty dlt-qty"
                                    max='{{ $product_details->total_allowed_quantity == 0 ? 'Infinity' : $product_details->total_allowed_quantity }}'
                                    step='{{ $product_details->quantity_step_size }}'
                                    min='{{ $product_details->minimum_order_quantity }}' />
                                <button class="qtyBtn plus" href="#;">+</button>
                            </div>
                        </div>
                        @php
                            if (count($product_details->variants) <= 1) {
                                $variant_id = $product_details->variants[0]->id;
                                $variant_price = $product_details->variants[0]->special_price;
                            } else {
                                $variant_id = '';
                                $variant_price = '';
                            }
                        @endphp
                        {{-- {{ dd($product_details)}} --}}
                        <div class="product-form-submit addcart fl-1 ms-3">
                            <button type="submit" name="add"
                                class="btn btn-primary product-form-cart-submit add_cart dlt-add-cart d-flex gap-2 btn-shadow-primary"
                                id="add_cart" data-product-variant-id="{{ $variant_id }}"
                                data-name='{{ $product_details->name }}'
                                data-slug='{{ $product_details->slug }}'
                                data-brand-name='{{ $product_details->brand_name }}'
                                data-image='{{ $product_details->image }}' data-product-type='regular'
                                data-max='{{ $product_details->total_allowed_quantity }}'
                                data-step='{{ $product_details->quantity_step_size }}'
                                data-min='{{ $product_details->minimum_order_quantity }}'
                                data-stock-type='{{ $product_details->stock_type }}'
                                data-store-id='{{ $product_details->store_id }}'
                                data-variant-price="{{ $variant_price }}">
                                <svg width="14" height="14" viewBox="0 0 38 38" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.06348 1C6.12453 1 7.88781 2.53768 8.16992 4.57715L8.4834 6.83301H19.7295C20.408 6.83318 20.958 7.38397 20.958 8.0625C20.958 8.74103 20.408 9.29182 19.7295 9.29199H8.8252L10.5088 21.417H28.7129C30.8981 21.4168 32.7988 19.8597 33.2275 17.7168L34.3311 12.1963V12.1953L34.3623 12.0732C34.5471 11.4802 35.1591 11.1022 35.7822 11.2334H35.7812C36.4457 11.3681 36.874 12.013 36.7402 12.6777L36.7412 12.6787L35.6367 18.1982C34.9789 21.4885 32.069 23.8748 28.7129 23.875H10.8955C11.4422 25.8346 13.2516 27.25 15.3281 27.25H29.9375C30.6161 27.25 31.167 27.8008 31.167 28.4795C31.1668 29.158 30.616 29.708 29.9375 29.708H15.3281C11.933 29.708 9.00502 27.2513 8.38574 23.9404L8.33301 23.6172L5.73535 4.91406C5.62079 4.08517 4.90205 3.45812 4.06543 3.45801H2.22949C1.55096 3.45801 1.00018 2.90799 1 2.22949C1 1.55085 1.55085 1 2.22949 1H4.06348ZM29.9375 1C30.6161 1 31.167 1.55085 31.167 2.22949V6.83301H35.7705C36.4492 6.83301 37 7.38386 37 8.0625C37 8.74114 36.4492 9.29199 35.7705 9.29199H31.167V13.8955C31.167 14.5742 30.6161 15.125 29.9375 15.125C29.2589 15.125 28.708 14.5742 28.708 13.8955V9.29199H24.1045C23.4259 9.29199 22.875 8.74114 22.875 8.0625C22.875 7.38386 23.4259 6.83301 24.1045 6.83301H28.708V2.22949C28.708 1.55085 29.2589 1 29.9375 1Z"
                                        fill="white" stroke="white" />
                                    <circle cx="28" cy="35" r="3" fill="white" />
                                    <circle cx="11" cy="35" r="3" fill="white" />
                                </svg>
                                <span>{{ labels('front_messages.add_to_cart', 'Add to cart') }}</span>
                            </button>
                        </div>
                        <div class="product-form-submit buyit fl-1 ms-3">
                            <button type="submit"
                                class="btn btn-secondary buy_now add_cart dlt-add-cart d-flex gap-2 btn-shadow-primary"
                                data-product-variant-id="{{ $variant_id }}"
                                data-name='{{ $product_details->name }}'
                                data-slug='{{ $product_details->slug }}'
                                data-brand_name='{{ $product_details->brand_name }}'
                                data-image='{{ $product_details->image }}' data-product-type='regular'
                                data-max='{{ $product_details->total_allowed_quantity }}'
                                data-step='{{ $product_details->quantity_step_size }}'
                                data-min='{{ $product_details->minimum_order_quantity }}'
                                data-store-id='{{ $product_details->store_id }}'
                                data-variant-price="{{ $variant_price }}">
                                <svg width="14" height="14" viewBox="0 0 38 38" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.0837 38C12.8326 38 14.2503 36.5822 14.2503 34.8333C14.2503 33.0844 12.8326 31.6666 11.0837 31.6666C9.33476 31.6666 7.91699 33.0844 7.91699 34.8333C7.91699 36.5822 9.33476 38 11.0837 38Z"
                                        fill="white" />
                                    <path
                                        d="M26.9167 38C28.6656 38 30.0833 36.5822 30.0833 34.8333C30.0833 33.0844 28.6656 31.6666 26.9167 31.6666C25.1678 31.6666 23.75 33.0844 23.75 34.8333C23.75 36.5822 25.1678 38 26.9167 38Z"
                                        fill="white" />
                                    <path
                                        d="M37.5011 2.11531C37.2042 1.81848 36.8015 1.65173 36.3817 1.65173C35.9618 1.65173 35.5592 1.81848 35.2622 2.11531L27.0938 10.2916L24.6381 7.72823C24.494 7.57831 24.3218 7.45825 24.1313 7.37489C23.9408 7.29153 23.7357 7.2465 23.5278 7.24239C23.3199 7.23827 23.1133 7.27514 22.9196 7.35089C22.726 7.42665 22.5492 7.5398 22.3992 7.6839C22.2493 7.82799 22.1293 8.0002 22.0459 8.19069C21.9625 8.38119 21.9175 8.58624 21.9134 8.79413C21.9051 9.21399 22.0639 9.61996 22.3549 9.92273L24.9104 12.5811C25.1827 12.8753 25.5118 13.1111 25.8779 13.2744C26.2439 13.4377 26.6392 13.525 27.04 13.5311H27.0922C27.485 13.5324 27.8741 13.4557 28.237 13.3054C28.5998 13.1551 28.9292 12.9342 29.206 12.6556L37.5011 4.35415C37.7979 4.05723 37.9647 3.65457 37.9647 3.23473C37.9647 2.81489 37.7979 2.41223 37.5011 2.11531Z"
                                        fill="white" />
                                    <path
                                        d="M34.675 14.2753C34.4703 14.2383 34.2603 14.2421 34.057 14.2863C33.8537 14.3305 33.6612 14.4143 33.4903 14.533C33.3195 14.6516 33.1736 14.8028 33.0612 14.9778C32.9488 15.1529 32.872 15.3483 32.8352 15.5531L32.6325 16.6757C32.435 17.7718 31.8585 18.7638 31.0039 19.478C30.1492 20.1923 29.0707 20.5835 27.9569 20.5833H8.5785L7.09017 7.91667H17.4167C17.8366 7.91667 18.2393 7.74985 18.5363 7.45292C18.8332 7.15599 19 6.75326 19 6.33333C19 5.91341 18.8332 5.51068 18.5363 5.21375C18.2393 4.91682 17.8366 4.75 17.4167 4.75H6.7165L6.65 4.19267C6.51355 3.03778 5.95818 1.97308 5.08913 1.20033C4.22008 0.427586 3.09775 0.000495324 1.93483 0L1.58333 0C1.16341 0 0.76068 0.166815 0.463748 0.463748C0.166815 0.76068 0 1.16341 0 1.58333C0 2.00326 0.166815 2.40599 0.463748 2.70292C0.76068 2.99985 1.16341 3.16667 1.58333 3.16667H1.93483C2.32264 3.16672 2.69695 3.3091 2.98676 3.5668C3.27656 3.8245 3.46171 4.1796 3.50708 4.56475L5.68575 23.0898C5.91193 25.0163 6.83759 26.7928 8.28702 28.082C9.73645 29.3712 11.6088 30.0834 13.5486 30.0833H30.0833C30.5033 30.0833 30.906 29.9165 31.2029 29.6196C31.4999 29.3227 31.6667 28.9199 31.6667 28.5C31.6667 28.0801 31.4999 27.6773 31.2029 27.3804C30.906 27.0835 30.5033 26.9167 30.0833 26.9167H13.5486C12.5662 26.9169 11.6078 26.6124 10.8056 26.0453C10.0034 25.4782 9.39681 24.6762 9.06933 23.75H27.9569C29.813 23.7501 31.6102 23.098 33.0344 21.9077C34.4586 20.7174 35.4192 19.0644 35.7485 17.2378L35.9512 16.1136C36.0254 15.7006 35.9328 15.2751 35.6935 14.9304C35.4542 14.5857 35.0879 14.3501 34.675 14.2753Z"
                                        fill="white" />
                                </svg>
                                <span>{{ labels('front_messages.buy_it_now', 'Buy it now') }}</span>
                            </button>
                        </div>
                    </div>

                    @if (!empty($product_details->sku))
                        <p class="product-sku mb-10px">{{ labels('front_messages.sku', 'SKU') }}:<span
                                class="text fw-500">{{ $product_details->sku }}</span></p>
                    @endif

                    @if (count($product_details->tags) >= 1)
                        <p class="text-uppercase text-black mb-10px"><ion-icon name="pricetags-outline"
                                class="custom-icon fs-6 me-1"></ion-icon>
                            @foreach ($product_details->tags as $tag)
                                <a href="{{ customUrl('products/?tag=' . $tag) }}"
                                    class="text fw-500 border-2 px-1 tag-filter"
                                    title="{!! $tag !!}">{!! $tag !!}
                                </a>
                            @endforeach
                        </p>
                    @endif
                    @if ($product_details->stock_type != '')
                        <div class="product-availability mb-10px position-static col-lg-9">
                            <div class="lh-1 d-flex justify-content-between">
                                <div class="text-sold fw-600 text-success">
                                    {{ labels('front_messages.currently', 'Currently') }}
                                    , <strong class="text-link"></strong>
                                    {{ labels('front_messages.items_in_stock', 'Items are in stock!') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- <hr class="light-hr" /> --}}
                    <div class="product-info">
                        @if ($product_details->product_type == 'digital_product')
                            <div class="freeShipMsg featureText mb-2 d-flex"><ion-icon name="cube-outline"
                                    class="fs-5 me-2"></ion-icon>{{ labels('front_messages.digital_product', 'Digital Product') }}
                            </div>
                        @else
                            <div class="freeShipMsg featureText mb-2 d-flex"><ion-icon name="ribbon-outline"
                                    class="fs-5 me-2"></ion-icon>
                                @if (!empty($product_details->guarantee_period))
                                    <b class="freeShip me-1">{{ $product_details->guarantee_period }}</b>
                                @else
                                    {{ labels('front_messages.no_guarantee', 'No Guarantee') }}
                                @endif
                            </div>
                            <div class="freeShipMsg featureText mb-2 d-flex"><ion-icon
                                    name="shield-checkmark-outline" class="fs-5 me-2"></ion-icon>
                                @if (!empty($product_details->warranty_period))
                                    <b class="freeShip me-1">{{ $product_details->warranty_period }}</b>
                                @else
                                    {{ labels('front_messages.no_warranty', 'No Warranty') }}
                                @endif
                            </div>
                            <div class="freeShipMsg featureText mb-2 d-flex"><ion-icon name="refresh-outline"
                                    class="fs-5 me-2"></ion-icon>{{ $product_details->is_returnable == 1 ? 'Returnable' : 'Non Returnable' }}
                            </div>
                            <div class="freeShipMsg featureText mb-2 d-flex"><ion-icon name="pin-outline"
                                    class="fs-5 me-2"></ion-icon>
                                {{ $product_details->cod_allowed == 1 ? 'Cash on Delivery available' : 'Cash on Delivery Not available' }}
                            </div>
                            <div class="freeShipMsg featureText mb-2 d-flex"><ion-icon
                                    name="shield-checkmark-outline" class="fs-5 me-2"></ion-icon>
                                @if ($product_details->is_cancelable == 1)
                                    <b class="freeShip me-1">{{ labels('front_messages.cancel_till', 'Cancel Till') }}
                                        {{ $product_details->cancelable_till }}</b>
                                @else
                                    {{ labels('front_messages.non_cancelable', 'Non Cancelable') }}
                                @endif
                            </div>
                        @endif

                        @if ($product_details->stock_type != '')
                            <p class="product-stock d-flex">
                                {{ labels('front_messages.availability', 'Availability') }}:
                                <span class="pro-stockLbl ps-0">
                                    @if ($product_details->availability >= 1)
                                        <span
                                            class="d-flex-center stockLbl instock text-uppercase">{{ labels('front_messages.in_stock', 'In stock') }}</span>
                                    @else
                                        <span
                                            class="d-flex-center stockLbl outstock text-uppercase text-danger">{{ labels('front_messages.out_of_stock', 'Out of Stock') }}</span>
                                    @endif
                                </span>
                            </p>
                        @endif
                        {{-- <hr class="light-hr" /> --}}
                        <div class="freeShipMsg featureText mb-2 d-flex align-items-center gap-2 fw-600 fs-6">
                            <span class="seller-icon"><ion-icon name="storefront-outline"></ion-icon></span>
                            <div class="d-flex flex-column">
                                <a wire:navigate
                                    href="{{ customUrl('sellers/' . $product_details->seller_slug) }}">{{ $product_details->seller_name }}</a>
                                <div class="product-review d-flex-center mb-2 gap-2">
                                    <input id="input-3-ltr-star-md" name="input-3-ltr-star-md"
                                        class="kv-ltr-theme-svg-star rating-loading d-none"
                                        value="{{ $product_details->seller_rating }}" dir="ltr"
                                        data-size="xs" data-show-clear="false" data-show-caption="false"
                                        readonly></span> <span
                                        class="product-review-number">{{ $product_details->seller_rating }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- @dd($product_details) --}}
                        @if ($product_details->product_type != 'digital_product')
                            <hr class="light-hr" />
                            @if ($deliverabilitySettings != false)
                                <p class="featureText">
                                    {{ labels('front_messages.check_product_deliverability', 'Check Product Deliverability') }}
                                </p>
                                <div class="row align-items-center">
                                    @if ($deliverabilitySettings[0]->product_deliverability_type == 'city_wise_deliverability')
                                        <div class="col-md-10 city_list_div">
                                            <div>
                                                <label for="city"
                                                    class="d-none">{{ labels('front_messages.city', 'City') }}
                                                    <span class="required">*</span></label>
                                                <select class="col-md-12 form-control city_list" id="city_list"
                                                    name="city">
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-10">
                                            <label for="pincode"
                                                class="d-none">{{ labels('front_messages.pincode', 'Pincode') }}
                                                <span class="required-f">*</span></label>
                                            <input name="pincode" placeholder="Enter Pincode" value=""
                                                id="pincode" type="text">
                                        </div>
                                    @endif
                                    <div class="col-md-2 mt-2 mt-md-0">
                                        <button type="submit"
                                            class="btn rounded w-100 check-product-deliverability"><span>{{ labels('front_messages.check', 'Check') }}</span></button>
                                    </div>
                                    @if ($deliverabilitySettings[0]->product_deliverability_type == 'city_wise_deliverability')
                                        <p class="fw-400 text-danger text-small">
                                            {{ labels('front_messages.city_not_on_list', 'If your city is not on the list') }}
                                            {{ labels('front_messages.cannot_deliver', 'we cannot deliver the product there') }}.
                                        </p>
                                    @endif
                                    <p class="featureText deliverability-res"></p>
                                    <input type="hidden" name="product_deliverability_type"
                                        id="product_deliverability_type"
                                        value="{{ $deliverabilitySettings[0]->product_deliverability_type }}">
                                    <input type="hidden" name="product_id" id="product_id"
                                        value="{{ $product_id }}">
                                    <input type="hidden" name="product_type" id="product_type" value="regular">
                                </div>
                            @endif
                        @endif
                    </div>


                    <livewire:pages.customer-ratings-list :product_id="$product_id" :product_details="$product_details" />


            </div>
        </div>
    </div>
    {{-- @dd($product_details); --}}
    @if ($siblingsProduct['previous_product'] != null)
        <a wire:navigate href="{{ customUrl('products/' . $siblingsProduct['previous_product']->slug) }}"
            class="product-nav prev-pro clr-none d-flex-center justify-content-between border-radius"
            title="Previous Product">
            @php
                $previous_product_img = dynamic_image($siblingsProduct['previous_product']->image, 200);
            @endphp
            <span class="details">
                <span class="name fw-600">{{ $siblingsProduct['previous_product']->name }}</span>
                <span
                    class="price">{{ currentCurrencyPrice((float) $siblingsProduct['previous_product']->min_max_price['special_min_price'], true) }}</span>
            </span>
            <span class="img"><img class="rounded-0 rounded-start-0" src="{{ $previous_product_img }}"
                    alt="{{ $siblingsProduct['previous_product']->name }}" /></span>
        </a>
    @endif
    @if ($siblingsProduct['next_product'] != null)
        <a wire:navigate href="{{ customUrl('products/' . $siblingsProduct['next_product']->slug) }}"
            class="product-nav next-pro clr-none d-flex-center justify-content-between border-radius"
            title="Next Product">
            @php
                $next_product_img = dynamic_image($siblingsProduct['next_product']->image, 200);
            @endphp
            <span class="img"><img class="rounded-0 rounded-end-0" src="{{ $next_product_img }}"
                    alt="{{ $siblingsProduct['next_product']->name }}" /></span>
            <span class="details">
                <span class="name fw-600">{{ $siblingsProduct['next_product']->name }}</span>
                <span
                    class="price">{{ currentCurrencyPrice((float) $siblingsProduct['next_product']->min_max_price['special_min_price'], true) }}</span>
            </span>
        </a>
    @endif


</div>
{{-- @dd($relative_products); --}}
<!--Related Products-->
@if (count($relative_products) >= 1)
    <section class="section product-slider pb-0">
        <div class="container-fluid">
            @php
                $heading['title'] = labels('front_messages.related_products', 'Related Products');
                $heading['short_description'] =
                    labels('front_messages.products_related_to', 'Products Related to ') . $product_details->name;
            @endphp
            <x-utility.section_header.sectionHeaderTwo :$heading />

            <!--Product Grid-->
            <div class="swiper style1-mySwiper gp15 arwOut5 hov-arrow grid-products">
                <div class="swiper-wrapper">
                    @foreach ($relative_products as $details)
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
@endif
<!--End Related Products-->
<div class="pswp" tabindex="-1" role="dialog">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Share"></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>
<!--Product Video Modal-->
<div class="productVideo-modal modal fade" id="productVideo_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="ratio ratio-16x9 productVideo-wrap">
                    <iframe class="rounded-0" src="https://www.youtube.com/embed/NpEaa2P7qZI"
                        title="YouTube video" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @dd($product_details); --}}
<!-- Sticky Cart -->
<div class="stickyCart">
    <div class="container-fluid">
        <div id="stickycart-form" class="d-flex-center justify-content-center">
            @php
                $image = dynamic_image($product_details->image, 200);
            @endphp

            <div class="product-featured-img"><img class="blur-up lazyload" data-src="{{ $image }}"
                    src="{{ $image }}" alt="product" width="120" height="170" /></div>
            <div class="sticky-title ms-2 ps-1 pe-5">{{ $product_details->name }}</div>
            <div class="stickyOptions position-relative">
                <div class="selectedOpt sticky-cart-variant product_price">
                    @php
                        if ($product_details->type != 'variable_product') {
                            $price = currentCurrencyPrice($product_details->variants[0]->price, true);
                            $special_price =
                                isset($product_details->variants[0]->special_price) &&
                                $product_details->variants[0]->special_price > 0
                                    ? currentCurrencyPrice($product_details->variants[0]->special_price, true)
                                    : $price;
                        } else {
                            $max_price = currentCurrencyPrice($product_details->min_max_price['max_price'], true);
                            $special_min_price =
                                isset($product_details->min_max_price['special_min_price']) &&
                                $product_details->min_max_price['special_min_price'] > 0
                                    ? currentCurrencyPrice($product_details->min_max_price['special_min_price'], true)
                                    : $max_price;
                        }
                    @endphp

                    @if ($product_details->type != 'variable_product')
                        {{ $special_price }}
                    @else
                        {{ $max_price }} - {{ $special_min_price }}
                    @endif
                </div>

            </div>
            <div class="qtyField mx-2">
                <button class="qtyBtn minus" href="#;"><ion-icon name="remove-outline"></ion-icon></button>
                <input type="text" name="quantity" value="1" class="product-form-input qty dlt-qty"
                    max='{{ $product_details->total_allowed_quantity == 0 ? 'Infinity' : $product_details->total_allowed_quantity }}'
                    step='{{ $product_details->quantity_step_size }}'
                    min='{{ $product_details->minimum_order_quantity }}' />
                <button class="qtyBtn plus" href="#;"><ion-icon name="add-outline"></ion-icon></button>
            </div>
            <button type="submit" name="add"
                class="btn btn-secondary product-form-cart-submit add_cart dlt-add-cart"
                data-product-variant-id="{{ $variant_id }}"
                data-max='{{ $product_details->total_allowed_quantity }}'
                data-step='{{ $product_details->quantity_step_size }}'
                data-min='{{ $product_details->minimum_order_quantity }}'
                data-store-id='{{ $product_details->store_id }}' data-variant-price="{{ $variant_price }}"
                data-product-type='regular'>
                <span>{{ labels('front_messages.add_to_cart', 'Add to cart') }}</span>
            </button>
        </div>
    </div>
</div>
</div>
<script src="{{ asset('frontend/elegant/js/lightbox.js') }}" defer></script>
<script>
    function toggleInput(liElement) {
        var inputElement = liElement.querySelector('input[type="radio"]');
        if (inputElement) {
            inputElement.click();
        }
    }
</script>
