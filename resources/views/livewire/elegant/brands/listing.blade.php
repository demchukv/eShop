<div id="page-content">
    <!--Page Header-->
    <x-utility.breadcrumbs.breadcrumbOne :$breadcrumb />
    <!--End Page Header-->

    <div class="container-fluid">
        <div class="collection-style1 row col-row row-cols-xl-8 row-cols-lg-auto row-cols-md-4 row-cols-3">
            @if (count($brands) >= 1)
                @foreach ($brands as $brand)
                    <div class="category-item col-item zoomscal-hov">
                        @php
                            $brand_img = dynamic_image($brand->image,400);
                        @endphp
                        <a wire:navigate href="{{ customUrl('products/?brand=' . $brand->slug) }}" class="category-link clr-none">
                            <div class="zoom-scal zoom-scal-nopb brands-image">
                                <img class="blur-up lazyload w-100" data-src="{{ $brand_img }}"
                                    src="{{ $brand_img }}" alt="{{ $brand->name }}" title="{{ $brand->name }}" />
                            </div>
                            <div class="details mt-3 d-flex justify-content-center align-items-center">
                                <h4 class="category-title mb-0">{{ $brand->name }}</h4>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
