@php
    $store_settings = getStoreSettings();
@endphp


<div wire:ignore id="page-content" class="index-demo1">


    <x-utility.categories.sliders.freeSliderV1 />


    {{-- <section class="slideshow slideshow-wrapper slideshow-medium">
        <div class="swiper mySwiper home-mySwiper">
            <div class="swiper-wrapper">
                @foreach ($sliders as $slider)
                    @php
                        $img = getMediaImageUrl($slider['image']);
                    @endphp
                    <div class="swiper-slide slideshow-wrap">
                        <a wire:navigate href="{{ $slider['link'] }}" class="slider-link" data-link="{{ $slider['link'] }}"
                            target="{{ $slider['type'] == 'slider_url' ? '_blank' : '' }}">
                            <img class="rounded-4 blur-up lazyload" data-src="{{ $img }}"
                                src="{{ $img }}" alt="slideshow" title="" />
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section> --}}
    <!--Popular Categories-->
    @php
        $categories = $categories['categories'];
    @endphp
    @if (is_array($categories) && count($categories) >= 1)
        <section class="section collection-slider">
            <div class="container-fluid">
                <div class="section-header style2 d-flex-center justify-content-between">
                    <div class="section-header-left text-start d-flex gap-4 align-items-end">
                        @if ($store_settings['category_section_title'] != null)
                            <h2>{{ $store_settings['category_section_title'] }}</h2>
                        @else
                            <h2>{{ labels('front_messages.popular_categories', 'Popular Categories') }}</h2>
                        @endif
                        <p>{{ labels('front_messages.explore_categories', 'Explore top picks in our Categories!') }}</p>
                    </div>
                    <div class="section-header-right text-start text-sm-end mt-3 mt-sm-0">

                        <a wire:navigate href="{{ customUrl('categories') }}" wire:navigate
                            class="d-flex align-items-center view_more_icon">
                            <ion-icon wire:ignore name="arrow-forward-circle" class="me-1 fs-1"></ion-icon></a>
                    </div>
                </div>
                <x-utility.categories.sliders.sliderThree :$categories />
            </div>
        </section>
    @endif

    @if (is_array($categories_section) && count($categories_section) >= 1)
        @foreach ($categories_section as $category_section)
            <section class="section collection-slider">
                <div class="container-fluid">
                    <div class="section-header style2 d-flex-center justify-content-between">
                        <div class="section-header-left text-start d-flex gap-4 align-items-end">
                            <h2>{{ $category_section->title }}</h2>
                            <p>{{ labels('front_messages.explore_categories', 'Explore top picks in our Categories!') }}
                            </p>
                        </div>
                        <div class="section-header-right text-start text-sm-end mt-3 mt-sm-0">

                            <a wire:navigate href="{{ customUrl('categories') }}" wire:navigate
                                class="d-flex align-items-center view_more_icon">
                                <ion-icon wire:ignore name="arrow-forward-circle" class="me-1 fs-1"></ion-icon></a>
                        </div>
                    </div>
                    @php
                        $categories = $category_section->categories_detail;
                    @endphp
                    <x-utility.categories.sliders.sliderThree :$categories />
                </div>
            </section>
        @endforeach
    @endif

    <!--Popular brands-->
    @if (is_array($brands['brands']) && count($brands['brands']) >= 1)
        <section class="section collection-slider">
            <div class="container-fluid">
                <div class="section-header style2 d-flex-center justify-content-between">
                    <div class="section-header-left text-start d-flex gap-4 align-items-end">
                        <h2>{{ labels('front_messages.popular_brands', 'Popular Brands') }}</h2>
                        <p>{{ labels('front_messages.explore_brands', 'Explore top picks in our Brands!') }}</p>
                    </div>
                    <div class="section-header-right text-start text-sm-end mt-3 mt-sm-0">


                        <a wire:navigate href="{{ customUrl('brands') }}" wire:navigate
                            class="d-flex align-items-center view_more_icon">
                            <ion-icon wire:ignore name="arrow-forward-circle" class="me-1 fs-1"></ion-icon></a>
                    </div>
                </div>
                <div class="swiper category-mySwiper">
                    <div class="swiper-wrapper">
                        @foreach ($brands['brands'] as $brand)
                            <div class="swiper-slide slider-brand zoomscal-hov">
                                <div class="light-bg brand-slider-paddings">
                                    <a wire:navigate href="{{ customUrl('products/?brand=' . $brand['brand_slug']) }}"
                                        class="category-link brand-box slider-link"
                                        data-link="{{ customUrl('products/?brand=' . $brand['brand_slug']) }}">
                                        <div class="zoom-scal zoom-scal-nopb  img-box-rounded">
                                            <img class="blur-up lazyload" data-src="{{ $brand['brand_img'] }}"
                                                src="{{ $brand['brand_img'] }}" alt="collection" title=""
                                                style="border-radius:100%; overflow:hidden;" />
                                        </div>
                                        @if (($store_settings['brand_style'] ?? null) == 'brands_style_1')
                                            <div class="details text-center bg-body">
                                                <h4 class="category-title mb-0 fs-6 fw-600 text-capitalize">
                                                    {!! $brand['brand_name'] !!}
                                                </h4>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </section>
    @endif

    <!--Products With Tabs-->
    @foreach ($sections as $count_key => $row)
        @if (!empty($row->product_details))
            @if ($row->style == 'style_1')
                <section class="section product-slider tab-slider-product">
                    <div class="container-fluid">
                        <x-utility.section_header.sectionHeaderOne :title="$row" />
                        <div class="swiper style1-mySwiper gp15 arwOut5 hov-arrow grid-products">
                            <div class="swiper-wrapper">
                                @foreach ($row->product_details as $details)
                                    <div class="swiper-slide ">
                                        <x-utility.cards.productCardOne :$details />
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </section>
            @endif
            @if ($row->style == 'style_2')
                <section class="section product-banner-slider pt-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                                <div
                                    class="grid-products swiper style2-mySwiper gp15 arwOut5 hov-arrow circle-arrow arrowlr-0">
                                    <div class="swiper-wrapper">
                                        @foreach ($row->product_details as $details)
                                            <div class="swiper-slide ">
                                                <x-utility.cards.productCardTwo :details="$details" />
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 mt-4 mt-lg-0">
                                <div class="ctg-bnr-wrap two position-relative h-100">
                                    <div class="ctg-image ratio ratio-1x1 h-100">
                                        <img class="blur-up lazyload object-fit-cover"
                                            data-src="{{ $row->banner_image }}" src="{{ $row->banner_image }}"
                                            alt="collection" width="309" height="483" />
                                    </div>
                                    <div
                                        class="ctg-content text-white d-flex-justify-center flex-nowrap flex-column h-100">
                                        <h2 class="ctg-title text-white m-0">{{ $row->title }}</h2>
                                        <p class="ctg-des mt-1 mb-4">{{ $row->short_description }}</p>
                                        <a wire:navigate
                                            href="{{ customUrl('section/' . $row->slug . '/' . $row->id . '/' . ($row->product_type == 'custom_combo_products' ? 'combo-' : '') . 'products') }}"
                                            class="btn btn-secondary explore-btn button-style" href="">
                                            <span
                                                class="text button-text">{{ labels('front_messages.shop_now', 'Shop Now') }}</span>
                                            <span class="button-icon button-icon-right"><ion-icon
                                                    name="arrow-forward-outline"></ion-icon></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
            @if ($row->style == 'style_3')
                <!--Products Slider-->
                <section class="section product-banner-slider">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 mb-4 mb-lg-0">
                                <div class="ctg-bnr-wrap one position-relative h-100">
                                    <div class="ctg-image ratio ratio-1x1 h-100">
                                        <img class="blur-up lazyload object-fit-cover"
                                            data-src="{{ $row->banner_image }}" src="{{ $row->banner_image }}"
                                            alt="collection" width="390" height="483" />
                                    </div>
                                    <div
                                        class="ctg-content text-white d-flex-justify-center flex-nowrap flex-column h-100">
                                        <h2 class="ctg-title text-white m-0">{{ $row->title }}
                                        </h2>
                                        <p class="ctg-des mt-3 mb-4">{{ $row->short_description }}</p>
                                        <a wire:navigate
                                            href="{{ customUrl('section/' . $row->slug . '/' . $row->id . '/' . $row->id . '/' . ($row->product_type == 'custom_combo_products' ? 'combo-' : '') . 'products') }}"
                                            class="btn btn-secondary explore-btn"
                                            href="">{{ labels('front_messages.explore_now', 'Explore Now') }}
                                            <ion-icon class="ms-1" name="arrow-forward-outline"></ion-icon></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                                <div
                                    class="grid-products swiper style2-mySwiper gp15 arwOut5 hov-arrow circle-arrow arrowlr-0">
                                    <div class="swiper-wrapper">
                                        @foreach ($row->product_details as $details)
                                            <div class="swiper-slide ">
                                                <x-utility.cards.productCardTwo :details="$details" />
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endif
    @endforeach
    @if (
        $web_settings['support_mode'] == 1 ||
            $web_settings['shipping_mode'] == 1 ||
            $web_settings['safety_security_mode'] == 1 ||
            $web_settings['return_mode'] == 1)
        <section class="section service-section pb-0">
            <x-utility.others.serviceSection />
        </section>
    @endif
</div>
