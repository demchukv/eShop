@props(['title'])
<div class="section-header style2 d-flex-center justify-content-between">
    <div class="section-header-left text-start">
        <h2>{{ $title->title }}</h2>
        <p>{{ $title->short_description }}</p>
    </div>
    <div class="section-header-right text-start text-sm-end mt-3 mt-sm-0">
        @php
            if ($title->product_type == 'custom_combo_products') {
                $url = customUrl('section/' . $title->slug . '/' . $title->id . '/combo-products');
            } else {
                $url = customUrl('section/' . $title->slug . '/' . $title->id . '/products');
            }
        @endphp
        {{-- <a wire:navigate href="{{ $url }}"
            class="btn btn-sm btn-outline-primary button-style">
            <span class="text button-text-style2">{{ labels('front_messages.view_more', 'View More') }}</span>
            <span class="button-icon button-icon-right-style2"><ion-icon
                    name="arrow-forward-outline"></ion-icon></span></a> --}}
        <a wire:navigate href="{{ $url }}" wire:navigate class="d-flex align-items-center view_more_icon">
            <ion-icon wire:ignore name="arrow-forward-circle" class="me-1 fs-1"></ion-icon></a>
    </div>
</div>
