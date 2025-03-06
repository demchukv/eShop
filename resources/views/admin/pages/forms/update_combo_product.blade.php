@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.combo_products', 'Combo Products') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.update_product', 'Update Product') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.add_products_with_power_and_simplicity', 'Add products with power and simplicity') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ labels('admin_labels.products', 'Products') }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ labels('admin_labels.manage_products', 'Manage Products') }}
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.add_product', 'Add Product') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Basic Layout -->

    <section class="overview-statistic">

        <div class="col-xxl-12 p-0">
            <div class="row cols-5 d-flex">
                <div class="col-md-12 col-xl-3">
                    <div class="card p-5">
                        <div class="card1">
                            <ul id="progressbar" class="text-center">
                                <li class="active step0"></li>
                                <li class="step0"></li>
                                <li class="step0"></li>
                                <li class="step0 {{ $data->product_type == 'digital_product' ? 'd-none' : '' }}"></li>
                                <li class="step0 {{ $data->product_type == 'digital_product' ? 'd-none' : '' }}"></li>
                                <li class="step0"></li>
                                <li class="step0"></li>
                                <li class="step0"></li>
                            </ul>

                            <h6 class="mt-1">
                                {{ labels('admin_labels.select_product_type_and_category', 'Select Product Type') }}
                            </h6>
                            <h6>{{ labels('admin_labels.product_information', 'Product Information') }}
                            </h6>
                            <h6>{{ labels('admin_labels.product_tax', 'Product Tax') }}
                            </h6>
                            <h6 class="{{ $data->product_type == 'digital_product' ? 'd-none' : '' }}">
                                {{ labels('admin_labels.product_quantity_and_other', 'Product Quantity & Other') }}
                            </h6>
                            <h6 class="{{ $data->product_type == 'digital_product' ? 'd-none' : '' }}">
                                {{ labels('admin_labels.delivery_and_shipping_setting', 'Delivery and Shipping Setting') }}
                            </h6>
                            <h6>{{ labels('admin_labels.products_additional_info', 'Products Additional Info') }}
                            </h6>
                            <h6>{{ labels('admin_labels.product_media', 'Product Media') }}
                            </h6>
                            <h6>{{ labels('admin_labels.product_description', 'Product Description') }}
                            </h6>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-9 mt-md-2 mt-sm-2 mt-xl-0">
                    <form action="{{ route('admin.combo_products.update', $data->id) }}" class="submit_form"
                        enctype="multipart/form-data" method="POST" id="">
                        @method('PUT')
                        @csrf
                        @if (isset($data->id))
                            <input type="hidden" name="edit_combo_product_id"
                                value="<?= isset($data->id) ? $data->id : '' ?>">
                            <input type="hidden" name="seller_id"
                                value="<?= isset($data->seller_id) ? $data->seller_id : '' ?>">
                            <input type="hidden" class='main_combo_seller_id'
                                value="<?= isset($data->seller_id) ? $data->seller_id : '' ?>">
                        @endif
                        <div class="card2 first-screen ml-2 show">
                            <div class="row">
                                <div class="col col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.choose_seller', 'Choose Seller') }}
                                        </h6>
                                        <select class='form-control' name='seller_id' id="seller_id" disabled>
                                            @php
                                                $store_id = getStoreId();
                                            @endphp
                                            <option value="{{ $sellers[0]->seller_id }}"
                                                {{ isset($data->seller_id) && $data->seller_id == $sellers[0]->seller_id ? 'selected' : '' }}>
                                                {{ $sellers[0]->username . ' - ' . $sellers[0]->store_name . ' (store)' }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.choose_product_type', 'Choose Product Type') }}</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group product-type-box">
                                                    <label class="form-check-label"
                                                        for="product_type_menu">{{ labels('admin_labels.physical_product', 'Physical Product') }}
                                                    </label>
                                                    <input class="form-check-input m-0" type="radio"
                                                        name="product_type_in_combo" value="physical_product"
                                                        id="product_type_menu" disabled
                                                        <?= $data->product_type == 'physical_product' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group product-type-box">
                                                    <label class="form-check-label"
                                                        for="product_type_menu">{{ labels('admin_labels.digital_product', 'Digital Product') }}
                                                    </label>
                                                    <input class="form-check-input m-0" type="radio"
                                                        name="product_type_in_combo" value="digital_product"
                                                        id="product_type_menu" disabled
                                                        <?= $data->product_type == 'digital_product' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-end ml-2 mt-xxl-3 mt-7 combo-next-button text-center" data-step="step1">
                                <button type="button"
                                    class="btn btn-primary">{{ labels('admin_labels.next', 'Next') }}</button>
                            </div>
                        </div>

                        <div class="card2 ml-2 ">
                            <div class="row">
                                <div class="col col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.product_information', 'Product Information') }}</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="pro_input_text"
                                                        class="form-label">{{ labels('admin_labels.product_name', 'Product Name') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <input type="text" class="form-control" id="pro_input_text"
                                                        placeholder="Product Name" name="title"
                                                        value="{{ $data->title ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tags"
                                                        class="form-label">{{ labels('admin_labels.tags', 'Tags') }}</label>
                                                    <input type="text" class="form-control" id="tags"
                                                        placeholder="dress,milk,almond" name="tags"
                                                        value="{{ isset($data->tags) && !empty($data->tags) ? $data->tags : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pro_short_description"
                                                        class="form-label">{{ labels('admin_labels.short_description', 'Short Description') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <textarea class="form-control" id="short_description" placeholder="Product Short Description"
                                                        name="short_description">{{ $data->short_description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div
                                                class="form-group physical_product_in_combo col-md-12 {{ isset($data->id) && $data->product_type == 'physical_product' ? '' : 'd-none' }}">
                                                <div class="mb-3 search_admin_product_for_combo_parent">
                                                    <label class="form-label"
                                                        for="name">{{ labels('admin_labels.physical_product', 'PhysicalProducts') }}<span
                                                            class='text-asterisks text-sm'>*</span></label>
                                                    <select name="physical_product_variant_id[]"
                                                        class="search_admin_product_for_combo w-100" multiple
                                                        data-placeholder="Type to search and select products"
                                                        onload="multiselect()">

                                                        @isset($data->id)
                                                            @foreach ($product_details as $row)
                                                                <option value="{{ $row['id'] }}" selected>
                                                                    {{ $row['name'] }}
                                                                    @if (!empty($row['variant_name']))
                                                                        - {{ $row['variant_name'] }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        @endisset
                                                    </select>



                                                </div>
                                            </div>
                                            <div
                                                class="form-group digital_product_in_combo col-md-12 {{ isset($data->id) && $data->product_type == 'digital_product' ? '' : 'd-none' }}">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="name">{{ labels('admin_labels.digital_product', 'Digital Product') }}<span
                                                            class='text-asterisks text-sm'>*</span></label>
                                                    <select id="" name="digital_product_id[]" value=""
                                                        class="select2 form-select search_admin_digital_product  w-100"
                                                        multiple>
                                                        @isset($data->id)
                                                            @foreach ($product_details as $row)
                                                                <option
                                                                    value="{{ isset($data->id) && $data->product_type == 'digital_product' ? $row['id'] : '' }}"
                                                                    selected>
                                                                    {{ $row['name'] }}
                                                                </option>
                                                            @endforeach
                                                        @endisset
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label for="tags"
                                                        class="form-label">{{ labels('admin_labels.has_similar_product', 'Has Similar Product') }}?</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input has_similar_product"
                                                            type="checkbox" id="" name="has_similar_product"
                                                            {{ isset($data->has_similar_product) && $data->has_similar_product == '1' ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12 {{ $data->has_similar_product == '1' ? '' : 'd-none' }}"
                                                id='similar_product'>
                                                <label for="similar_product"
                                                    class="form-label">{{ labels('admin_labels.select_products', 'Select Products') }}
                                                    <span class='text-asterisks text-sm'>*</span></label>
                                                <select id="" name="similar_product_id[]" value=""
                                                    data-update=1 data-product_id={{ $data->id }}
                                                    class="select2 form-select search_admin_combo_product  w-100" multiple>
                                                    @if (isset($data->id))
                                                        @foreach ($combo_product_details as $row)
                                                            <option value="{{ $row['id'] }}" selected>
                                                                {{ $row['title'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="float-end ml-2 mt-xxl-3 mt-7 combo-next-button text-center" data-step="step2">
                                <button type="button"
                                    class="btn btn-primary ">{{ labels('admin_labels.next', 'Next') }}</button>
                            </div>
                        </div>

                        <div class="card2 ml-2">
                            <div class="row">
                                <div class="col col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.product_tax', 'Product Tax') }}</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    @php
                                                        $taxes =
                                                            isset($data->tax) && $data->tax != null
                                                                ? explode(',', $data->tax)
                                                                : [];
                                                    @endphp

                                                    <label for="pro_input_tax"
                                                        class="form-label">{{ labels('admin_labels.select_tax', 'Select Tax') }}</label>
                                                    <select name="pro_input_tax[]" class="tax_list form-select w-100"
                                                        multiple>
                                                        @php
                                                            $tax_name = fetchDetails(
                                                                'taxes',
                                                                '',
                                                                ['title', 'percentage', 'id'],
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                'id',
                                                                $taxes,
                                                            );
                                                        @endphp

                                                        @foreach ($tax_name as $row)
                                                            <option value="{{ $row->id }}"
                                                                {{ in_array($row->id, $taxes) ? 'selected' : '' }}>
                                                                {{ $row->title }} ({{ $row->percentage }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-7">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <label for="is_prices_inclusive_tax"
                                                            class="form-label">{{ labels('admin_labels.tax_includes_in_price', 'Tax Includes In Price') }}</label>
                                                    </div>
                                                    <div class="d-flex">
                                                        <label for="" class="me-6 text-muted">[Enable/Disable]</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="" name="is_prices_inclusive_tax"
                                                                {{ isset($data->is_prices_inclusive_tax) && $data->is_prices_inclusive_tax == '1' ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-end ml-2 mt-xxl-3 mt-7 combo-next-button text-center" data-step="step3">
                                <button type="button"
                                    class="btn btn-primary ">{{ labels('admin_labels.next', 'Next') }}</button>
                            </div>
                        </div>
                        <div class="card2 ml-2 {{ $data->product_type == 'digital_product' ? 'd-none' : '' }}">
                            <div class="row">
                                <div class="col col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.product_quantity_and_other', 'Product Quantity & Other') }}
                                        </h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6 total_allowed_quantity">
                                                <div class="form-group">
                                                    <label for="total_allowed_quantity"
                                                        class="form-label">{{ labels('admin_labels.total_allowed_quantity', 'Total Allowed Quantity') }}</label>
                                                    <input type="number" class="col-md-12 form-control"
                                                        name="total_allowed_quantity" min=0
                                                        value="{{ isset($data->total_allowed_quantity) ? $data->total_allowed_quantity : '' }}"
                                                        placeholder="Total Allowed Quantity">
                                                </div>
                                            </div>
                                            <div class="col-md-6 minimum_order_quantity">
                                                <div class="form-group">
                                                    <label for="minimum_order_quantity"
                                                        class="form-label">{{ labels('admin_labels.minimum_order_quantity', 'Minimum Order Quantity') }}</label>
                                                    <input type="number" class="col-md-12 form-control"
                                                        name="minimum_order_quantity" min="1"
                                                        value="{{ isset($data->minimum_order_quantity) ? $data->minimum_order_quantity : '' }}"
                                                        placeholder="Minimum Order Quantity">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 quantity_step_size">
                                                <div class="form-group">
                                                    <label for="quantity_step_size"
                                                        class="form-label">{{ labels('admin_labels.quantity_step_size', 'Quantity Step Size') }}</label>
                                                    <input type="number" class="col-md-12 form-control"
                                                        name="quantity_step_size" min="1"
                                                        value="{{ isset($data->quantity_step_size) ? $data->quantity_step_size : '' }}"
                                                        placeholder="Quantity Step Size">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-end ml-2 mt-xxl-3 mt-7 combo-next-button text-center" data-step="step4">
                                <button type="button"
                                    class="btn btn-primary ">{{ labels('admin_labels.next', 'Next') }}</button>
                            </div>
                        </div>
                        <div class="card2 ml-2 {{ $data->product_type == 'digital_product' ? 'd-none' : '' }}">
                            <div class="row">
                                <div class="col col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.delivery_and_shipping_setting', 'Delivery And Shipping Setting') }}
                                        </h6>
                                        <hr>
                                        <div class="row">
                                            <div
                                                class="col-md-6 {{ isset($product_deliverability_type) && $product_deliverability_type == 'city_wise_deliverability' ? 'd-none' : '' }}">
                                                <div class="form-group">
                                                    <label for="zipcode"
                                                        class="form-label">{{ labels('admin_labels.deliverable_type', 'Deliverable Type') }}</label>
                                                    <select class="form-control form-select" name="deliverable_type"
                                                        id="deliverable_type">
                                                        <option value="0"
                                                            {{ $data->deliverable_type == '0' ? 'selected="selected"' : '' }}>
                                                            None
                                                        </option>
                                                        <option value="1"
                                                            {{ $data->deliverable_type == '1' ? 'selected' : '' }}>
                                                            All
                                                        </option>
                                                        <option value="2"
                                                            {{ $data->deliverable_type == '2' ? 'selected' : '' }}>
                                                            Included
                                                        </option>
                                                        <option value="3"
                                                            {{ $data->deliverable_type == '3' ? 'selected' : '' }}>
                                                            Excluded
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            @php
                                                $zones =
                                                    isset($data->deliverable_zones) && $data->deliverable_zones != null
                                                        ? explode(',', $data->deliverable_zones)
                                                        : [];
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="zipcodes"
                                                        class="form-label">{{ labels('admin_labels.deliverable_zones', 'Deliverable Zones') }}</label>
                                                    <select name="deliverable_zones[]"
                                                        class="search_zone form-select w-100" multiple
                                                        id="deliverable_zones"
                                                        {{ isset($data->deliverable_type) && ($data->deliverable_type == 2 || $data->deliverable_type == 3) ? '' : 'disabled' }}>
                                                        @if (isset($data->deliverable_type) && ($data->deliverable_type == 2 || $data->deliverable_type == 3))
                                                            @php
                                                                $zone_names = fetchDetails(
                                                                    'zones',
                                                                    '',
                                                                    [
                                                                        'name',
                                                                        'id',
                                                                        'serviceable_city_ids',
                                                                        'serviceable_zipcode_ids',
                                                                    ],
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    'id',
                                                                    $zones,
                                                                );

                                                                foreach ($zone_names as $zone) {
                                                                    $zone->serviceable_city_names = getCityNamesFromIds(
                                                                        $zone->serviceable_city_ids,
                                                                    );
                                                                    $zone->serviceable_zipcodes = getZipcodesFromIds(
                                                                        $zone->serviceable_zipcode_ids,
                                                                    );
                                                                }
                                                            @endphp

                                                            @foreach ($zone_names as $row)
                                                                <option value="{{ $row->id }}"
                                                                    {{ in_array($row->id, $zones) ? 'selected' : '' }}>
                                                                    ID - {{ $row->id }} | Name - {{ $row->name }}
                                                                    |
                                                                    Serviceable Cities:
                                                                    {{ implode(', ', $row->serviceable_city_names) }} |
                                                                    Serviceable Zipcodes:
                                                                    {{ implode(', ', $row->serviceable_zipcodes) }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_type"
                                                        class="form-label">{{ labels('admin_labels.for_standard_shipping', 'For Standard Shipping') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <select class='form-control form-select shiprocket_type'
                                                        name="pickup_location" id="pickup_location">
                                                        <option value=" ">Select Pickup Location</option>
                                                        @foreach ($shipping_data as $val)
                                                            @php
                                                                $pickup_location =
                                                                    isset($data->pickup_location) &&
                                                                    !empty($data->pickup_location)
                                                                        ? $data->pickup_location
                                                                        : '';
                                                            @endphp
                                                            <option value="{{ $val->pickup_location }}"
                                                                {{ $val->pickup_location == $pickup_location ? 'selected' : '' }}>
                                                                {{ $val->pickup_location }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="zipcodes"
                                                        class="form-label">{{ labels('admin_labels.minimum_free_delivery_order_quantity', 'Minimum Free Delivery Order Quantity') }}</label>
                                                    <input type="number" class="form-control" min=0
                                                        value="{{ isset($data->minimum_free_delivery_order_qty) ? $data->minimum_free_delivery_order_qty : '' }}"
                                                        name="minimum_free_delivery_order_qty">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="zipcodes"
                                                        class="form-label">{{ labels('admin_labels.delivery_charges', 'Delivery Charges') }}</label>
                                                    <input type="number" class="form-control" min=0
                                                        value="{{ isset($data->delivery_charges) ? $data->delivery_charges : '' }}"
                                                        name="delivery_charges">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-7">
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <label for="is_cod_allowed"
                                                                class="form-label">{{ labels('admin_labels.is_cod_allowed', 'IS COD Allowed') }}?</label>
                                                        </div>
                                                        <div class="d-flex">
                                                            <label for="" class="me-6 text-muted">[Enable/Disable]</label>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="" name="cod_allowed"
                                                                    {{ isset($data->cod_allowed) && $data->cod_allowed == '1' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mt-7 ">
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <label for="is_returnable"
                                                                class="form-label">{{ labels('admin_labels.is_returnable', 'IS Returnable') }}?</label>
                                                        </div>
                                                        <div class="d-flex">
                                                            <label for=""
                                                                class="me-6 text-muted form-label">[Enable/Disable]</label>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="" name="is_returnable"
                                                                    {{ isset($data->is_returnable) && $data->is_returnable == '1' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-7 ">
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <label for="is_cancelable"
                                                                class="form-label">{{ labels('admin_labels.is_cancelable', 'IS Cancelable') }}?</label>
                                                        </div>
                                                        <div class="d-flex">
                                                            <label for=""
                                                                class="me-6 text-muted form-label">[Enable/Disable]</label>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="is_cancelable_checkbox" name="is_cancelable"
                                                                    {{ isset($data->is_cancelable) && $data->is_cancelable == '1' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-6 mt-7 {{ isset($data->is_cancelable) && $data->is_cancelable == '1' ? '' : 'collapse' }}"
                                                id='cancelable_till'>
                                                <div class="form-group">
                                                    <label for="cancelable_till"
                                                        class="form-label">{{ labels('admin_labels.till_which_status', 'Till Which Status') }}?</label>
                                                    <select class='form-control form-select' name="cancelable_till">
                                                        <option value='received'
                                                            {{ $data->cancelable_till == 'received' ? 'selected' : '' }}>
                                                            Received</option>
                                                        <option value='processed'
                                                            {{ $data->cancelable_till == 'processed' ? 'selected' : '' }}>
                                                            Processed</option>
                                                        <option value='shipped'
                                                            {{ $data->cancelable_till == 'shipped' ? 'selected' : '' }}>
                                                            Shipped</option>
                                                    </select>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-end ml-2 mt-xxl-3 mt-7 combo-next-button text-center" data-step="step5">
                                <button type="button"
                                    class="btn btn-primary ">{{ labels('admin_labels.next', 'Next') }}</button>
                            </div>
                        </div>
                        <div class="card2 ml-2">
                            <div class="row">
                                <div class="col col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.products_additional_info', 'Product Additional Info') }}
                                        </h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12 additional-info existing-additional-settings">
                                                <div class="mt-4 col-md-12 additional-info-nav-header d-flex">
                                                    <div class="col-md-6">
                                                        <nav class="w-100">
                                                            <div class="nav nav-tabs" id="product-tab" role="tablist">
                                                                <a class="nav-item nav-link active"
                                                                    id="tab-for-general-price" data-bs-toggle="tab"
                                                                    href="#general-settings" role="tab"
                                                                    aria-controls="general-price"
                                                                    aria-selected="true">{{ labels('admin_labels.general', 'General') }}</a>
                                                                <a class="nav-item nav-link edit-product-attributes"
                                                                    id="tab-for-attributes" data-bs-toggle="tab"
                                                                    href="#product-attributes" role="tab"
                                                                    aria-controls="product-attributes"
                                                                    aria-selected="false">{{ labels('admin_labels.attributes', 'Attributes') }}</a>
                                                                <a class="nav-item nav-link d-none"
                                                                    id="tab-for-variations" data-bs-toggle="tab"
                                                                    href="#product-variants" role="tab"
                                                                    aria-controls="product-variants"
                                                                    aria-selected="false">{{ labels('admin_labels.variantions', 'Variations') }}</a>
                                                            </div>
                                                        </nav>
                                                    </div>
                                                    <input type="hidden" name="product_type" value="">
                                                    <input type="hidden" name="simple_product_stock_status">
                                                    <input type="hidden" name="variant_stock_level_type">
                                                    <input type="hidden" name="variant_stock_status">
                                                    <input type="hidden" id="combo_type" value="combo_product">
                                                </div>
                                                <div id="attributes_values_json_data" class="d-none">
                                                    <select class="select_single"
                                                        data-placeholder="Type to search and select attributes">
                                                        <option value=""></option>

                                                        @foreach ($attributes as $attribute)
                                                            @php
                                                                $attribute_data = json_encode(
                                                                    $attribute->attribute_values,
                                                                    1,
                                                                );
                                                            @endphp
                                                            <option name='{{ $attribute->name }}'
                                                                value='{{ $attribute->name }}'
                                                                data-values='{{ json_encode($attribute->attribute_values, 1) }}'>
                                                                {{ $attribute->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="tab-content p-3 col-md-12" id="nav-tabContent">
                                                    <div class="tab-pane fade active show" id="general-settings"
                                                        role="tabpanel" aria-labelledby="general-settings-tab">

                                                        <div id="product-general-settings">
                                                            <div id="general_price_section" class="">
                                                                <div class="row">
                                                                    <div class="col-md-6">

                                                                        <ul>
                                                                            <li>
                                                                                <h6>{{ labels('admin_labels.price_info', 'Price Info') }}
                                                                                </h6>
                                                                            </li>
                                                                        </ul>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="simple_price"
                                                                                    class="col-md-6 form-label">{{ labels('admin_labels.price', 'Price') }}:
                                                                                    <span
                                                                                        class="text-asterisks text-sm">*</span></label>
                                                                                <input type="number" name="simple_price"
                                                                                    class="form-control stock-simple-mustfill-field price"
                                                                                    min="0.01" step="0.01"
                                                                                    value="{{ $data->price }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="type"
                                                                                    class="col-md-6 form-label">{{ labels('admin_labels.special_price', 'Special Price') }}
                                                                                    : <span
                                                                                        class="text-asterisks text-sm">*</span></label>
                                                                                <input type="number"
                                                                                    name="simple_special_price"
                                                                                    class="form-control discounted_price"
                                                                                    min="0" step="0.01"
                                                                                    value="{{ $data->special_price }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="col-md-6 {{ $data->product_type == 'digital_product' ? 'd-none' : '' }}">
                                                                        <div class="dimensions " id="product-dimensions">

                                                                            <ul>
                                                                                <li>
                                                                                    <h6>{{ labels('admin_labels.standard_shipping_weightage', 'Standard shipping weightage') }}
                                                                                    </h6>
                                                                                </li>
                                                                            </ul>
                                                                            <div class="row">
                                                                                <div class="col-6">
                                                                                    <div class="form-group">
                                                                                        <label for="weight"
                                                                                            class="form-label col-md-12">{{ labels('admin_labels.weight', 'Weight') }}
                                                                                            <small>(kg)</small>
                                                                                            <span
                                                                                                class='text-asterisks text-xs'>*</span></label>
                                                                                        <input type="number"
                                                                                            class="form-control"
                                                                                            name="weight"
                                                                                            placeholder="Weight"
                                                                                            id="weight"
                                                                                            value="{{ $data->weight }}"
                                                                                            step="0.01" min=0>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <div class="form-group">
                                                                                        <label for="height"
                                                                                            class="form-label col-md-12">{{ labels('admin_labels.height', 'Height') }}
                                                                                            <small>(cms)</small></label>
                                                                                        <input type="number" min=0
                                                                                            class="form-control"
                                                                                            name="height"
                                                                                            placeholder="Height"
                                                                                            id="height"
                                                                                            value="{{ $data->height }}"
                                                                                            step="0.01">

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-6">
                                                                                    <div class="form-group">
                                                                                        <label for="breadth"
                                                                                            class="form-label col-md-12">{{ labels('admin_labels.bredth', 'Bredth') }}
                                                                                            <small>(cms)</small>
                                                                                        </label>
                                                                                        <input type="number" min=0
                                                                                            class="form-control"
                                                                                            name="breadth"
                                                                                            placeholder="Breadth"
                                                                                            id="breadth"
                                                                                            value="{{ $data->breadth }}"
                                                                                            step="0.01">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <div class="form-group">
                                                                                        <label for="length"
                                                                                            class="form-label col-md-12">{{ labels('admin_labels.length', 'Length') }}
                                                                                            <small>(cms)</small>
                                                                                        </label>
                                                                                        <input type="number" min=0
                                                                                            class="form-control"
                                                                                            name="length"
                                                                                            placeholder="Length"
                                                                                            id="length"
                                                                                            value="{{ $data->length }}"
                                                                                            step="0.01">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="form-group  simple_stock_management {{ $data->product_type == 'digital_product' ? 'd-none' : '' }}">
                                                                    <div class="col">
                                                                        <input type="checkbox"
                                                                            {{ $data->stock != null ? 'checked' : '' }}
                                                                            name="simple_stock_management_status"
                                                                            class="align-middle simple_stock_management_status form-check-input m-0">
                                                                        <span
                                                                            class="align-middle">{{ labels('admin_labels.enable_stock_management', 'Enable Stock Management') }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="simple-product-level-stock-management {{ $data->product_type == 'digital_product' ? 'd-none' : '' }} {{ (isset($data->id) && $data->stock_type == null) || $data->product_type == 'digital_product' ? 'collapse' : '' }}">
                                                                <div class="row d-flex">
                                                                    <div class="col col-xs-4 col-md-4">
                                                                        <div class="form-group">
                                                                            <label for=""
                                                                                class="form-label">{{ labels('admin_labels.sku', 'Sku') }}
                                                                                <span
                                                                                    class='text-asterisks text-xs'>*</span>:</label>
                                                                            <input type="text" name="product_sku"
                                                                                class="col form-control simple-pro-sku"
                                                                                value="{{ isset($data->sku) ? $data->sku : '' }}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col col-xs-4 col-md-4">
                                                                        <div class="form-group">
                                                                            <label for=""
                                                                                class="form-label">{{ labels('admin_labels.total_stock', 'Total Stock') }}
                                                                                <span
                                                                                    class='text-asterisks text-xs'>*</span>:</label>
                                                                            <input type="number" min="0"
                                                                                name="product_total_stock"
                                                                                class="col form-control stock-simple-mustfill-field"
                                                                                value="{{ isset($data->stock) ? $data->stock : '' }}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col col-xs-4 col-md-4">
                                                                        <div class="form-group">
                                                                            <label for=""
                                                                                class="form-label">{{ labels('admin_labels.stock_status', 'Stock Status') }}
                                                                                :</label>
                                                                            <select type="text"
                                                                                class="col form-control form-select stock-simple-mustfill-field"
                                                                                name="simple_product_stock_status"
                                                                                id="simple_product_stock_status">
                                                                                <option value="1"
                                                                                    {{ $data->stock_type != null && $data->availability == '1' ? 'selected' : '' }}>
                                                                                    In Stock
                                                                                </option>
                                                                                <option value="0"
                                                                                    {{ $data->stock_type != null && $data->availability == '0' ? 'selected' : '' }}>
                                                                                    Out Of Stock
                                                                                </option>
                                                                            </select>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group collapse simple-product-save">
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);"
                                                                        class="btn btn-dark save-settings float-end {{ $data->product_type == 'digital_product' ? 'd-none' : '' }}">{{ labels('admin_labels.save_settings', 'Save Settings') }}</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="digital_product_setting"
                                                            class="{{ $data->product_type == 'digital_product' ? '' : 'collapse' }}">
                                                            <div class="row form-group">
                                                                <div class="col-md-6 d-flex">
                                                                    <label for="download_allowed"
                                                                        class="col form-label">{{ labels('admin_labels.is_download_allowed', 'IS Download Allowed') }}?</label>
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="download_allowed" id="download_allowed"
                                                                            {{ $data->download_allowed != 'null' && $data->download_allowed == 1 ? 'checked' : '' }}>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 col-xs-6 {{ isset($data->download_type) ? '' : 'collapse' }}"
                                                                    id='download_type'>
                                                                    <label for="download_link_type"
                                                                        class="col form-label">{{ labels('admin_labels.download_link_type', 'Download Link Type') }}
                                                                    </label>
                                                                    <select class='form-control form-select'
                                                                        name="download_link_type" id="download_link_type">
                                                                        <option value=''>None</option>
                                                                        <option value='self_hosted'
                                                                            {{ isset($data->download_type) && $data->download_type == 'self_hosted' ? 'selected' : '' }}>
                                                                            Self Hosted
                                                                        </option>
                                                                        <option value='add_link'
                                                                            {{ isset($data->download_type) && $data->download_type == 'add_link' ? 'selected' : '' }}>
                                                                            Add Link</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 {{ isset($data->download_type) && $data->download_type == 'add_link' ? '' : 'd-none' }}"
                                                                    id="digital_link_container">
                                                                    <label for="video"
                                                                        class="col form-label ml-1">{{ labels('admin_labels.digital_product_link', 'Digital Product Link') }}
                                                                        <span
                                                                            class='text-asterisks text-sm'>*</span></label>
                                                                    <input type="url" class='form-control'
                                                                        name='download_link' id='download_link'
                                                                        value="{{ isset($data->download_type) && $data->download_type == 'add_link' ? $data->download_link : '' }}"
                                                                        placeholder="Paste digital product link or URL here">
                                                                </div>

                                                                <div class="container-fluid row image-upload-section">
                                                                </div>
                                                                <div class="form-group {{ isset($data->download_type) && $data->download_type == 'self_hosted' ? '' : 'd-none' }} mt-5"
                                                                    id="digital_media_container">
                                                                    <a class="media_link" data-input="pro_input_zip"
                                                                        data-isremovable="0"
                                                                        data-is-multiple-uploads-allowed="0"
                                                                        data-media_type="archive,document"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#media-upload-modal"
                                                                        value="Upload Photo">
                                                                        <div
                                                                            class="col-md-6 file_upload_box border file_upload_border">
                                                                            <div class="mt-2">
                                                                                <div class="col-md-12  text-center">
                                                                                    <div>
                                                                                        <p
                                                                                            class="caption text-dark-secondary">
                                                                                            Choose file for
                                                                                            product.</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                    <div class="row mt-3 image-upload-section">
                                                                        <div
                                                                            class="bg-white grow image product-image-container rounded shadow text-center m-2">
                                                                            <div class='image-upload-div'><img
                                                                                    class="img-fluid mb-2"
                                                                                    src={{ asset('/assets/admin/images/doc-file.png') }}
                                                                                    alt="Product File"
                                                                                    title="Product File">
                                                                            </div>
                                                                            <input type="hidden" name="pro_input_zip"
                                                                                value="{{ $data->download_link }}">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group mt-4">
                                                                    <div class="col float-end">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-dark save-digital-product-settings">{{ labels('admin_labels.save_settings', 'Save Settings') }}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="product-attributes" role="tabpanel"
                                                        aria-labelledby="product-attributes-tab">
                                                        <div class="d-flex">
                                                            <div class="info col-md-6 p-3" id="note">
                                                                <div class="col-12 d-flex align-center">
                                                                    <strong>{{ labels('admin_labels.note', 'Note') }}
                                                                        :</strong>
                                                                    <input type="checkbox" checked=""
                                                                        class="ml-3 my-auto custom-checkbox form-check-input ms-1 me-1"
                                                                        readonly>
                                                                    <span class="ml-3">check if the attribute is to be
                                                                        used for variation</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 d-flex justify-content-end">
                                                                <button type="button" id="add_attributes"
                                                                    class="btn  btn-primary m-2 btn-xs">{{ labels('admin_labels.add_attributes', 'Add Attributes') }}</button>
                                                                <a href="javascript:void(0);" id=""
                                                                    class="save_attributes btn btn-dark m-2 btn-xs d-none">{{ labels('admin_labels.save_attributes', 'Save Attributes') }}</a>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div id="attributes_process">
                                                            <div
                                                                class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-attributes-added">
                                                                <div class="col-md-12 text-center">
                                                                    {{ labels('admin_labels.no_product_attributes_are_added', 'No Product Attributes Are Added') }}!
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="product-variants" role="tabpanel"
                                                        aria-labelledby="product-variants-tab">
                                                        <div class="col-md-12">
                                                            <a href="javascript:void(0);" id="reset_variants"
                                                                class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm collapse">{{ labels('admin_labels.reset_variants', 'Reset Variants') }}</a>
                                                        </div>

                                                        <div class="clearfix"></div>
                                                        <div
                                                            class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-variants-added">
                                                            <div class="col-md-12 text-center">
                                                                {{ labels('admin_labels.no_product_variations_added', 'No Product Variations Added') }}!
                                                            </div>
                                                        </div>
                                                        <div id="variants_process" class="ui-sortable">
                                                            <div
                                                                class="form-group move p-2 pe-0 product-variant-selectbox ps-0 pt-3 rounded row">
                                                                <div class="col-1 text-center my-auto">
                                                                    <i class="fas fa-sort"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-end ml-2 mt-xxl-3 mt-7 combo-next-button text-center" data-step="step6">
                                <button type="button"
                                    class="btn btn-primary ">{{ labels('admin_labels.next', 'Next') }}</button>
                            </div>
                        </div>
                        <div class="card2 ml-2">
                            <div class="row">
                                <div class="col-6 col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.product_media', 'Product Media') }}(
                                            {{ labels('admin_labels.images', 'Images') }} )
                                        </h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for=""
                                                    class="form-label">{{ labels('admin_labels.main_image', 'Main Image') }}<span
                                                        class="text-asterisks text-sm">*</span></label>
                                                <div class="form-group">
                                                    <a class="media_link" data-input="image" data-isremovable="0"
                                                        data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                        data-bs-target="#media-upload-modal" value="Upload Photo">

                                                        <div class="col-md-12 file_upload_box border file_upload_border">
                                                            <div class="mt-2">
                                                                <div class="col-md-12  text-center">
                                                                    <div>
                                                                        <p class="caption text-dark-secondary">Choose image
                                                                            for product.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    @if (isset($data->id) && !empty($data->id))
                                                        <div class="container-fluid row image-upload-section">
                                                            <label for="" class="text-danger mt-3">*Only Choose When Update is
                                                                necessary</label>

                                                            <div
                                                                class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                                <div class='image-upload-div'>
                                                                    <img class="img-fluid mb-2"
                                                                        src="{{ route('admin.dynamic_image', [
                                                                            'url' => getMediaImageUrl($data->image),
                                                                            'width' => 150,
                                                                            'quality' => 90,
                                                                        ]) }}"
                                                                        alt="Not Found" />
                                                                </div>
                                                                <input type="hidden" name="image"
                                                                    value='<?= $data->image ?>'>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="container-fluid row image-upload-section">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for=""
                                                    class="form-label">{{ labels('admin_labels.other_images', 'Other Images') }}</label>
                                                <div class="form-group">
                                                    <a class="media_link" data-input="other_images[]"
                                                        data-isremovable="1" data-is-multiple-uploads-allowed="1"
                                                        data-bs-toggle="modal" data-bs-target="#media-upload-modal"
                                                        value="Upload Photo">

                                                        <div class="col-md-12 file_upload_box border file_upload_border">
                                                            <div class="mt-2">
                                                                <div class="col-md-12  text-center">
                                                                    <div>
                                                                        <p class="caption text-dark-secondary">Choose
                                                                            images for product.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <p class="image_recommendation mt-2">(Recommended Size : 180 x 180
                                                        pixels)</p>
                                                    <div class="container-fluid row image-upload-section">
                                                        @php
                                                            $other_images = json_decode($data->other_images);
                                                        @endphp
                                                        @if (!empty($other_images))
                                                            @foreach ($other_images as $row)
                                                                <div
                                                                    class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                                    <div class='image-upload-div'>
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ route('admin.dynamic_image', [
                                                                                'url' => getMediaImageUrl($row),
                                                                                'width' => 150,
                                                                                'quality' => 90,
                                                                            ]) }}"
                                                                            alt="Not Found" />
                                                                    </div>
                                                                    <a href="javascript:void(0)" class="delete-img m-3"
                                                                        data-id="<?= $data->id ?>"
                                                                        data-field="other_images" data-img="<?= $row ?>"
                                                                        data-table="combo_products"
                                                                        data-path="<?= $row ?>" data-isjson="true">
                                                                        <span
                                                                            class="btn btn-block bg-gradient-danger btn-xs"><i
                                                                                class="far fa-trash-alt "></i>
                                                                            Delete</span></a>
                                                                    <input type="hidden" name="other_images[]"
                                                                        value='<?= $row ?>'>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="float-end ml-2 mt-xxl-3 mt-7 combo-next-button text-center" data-step="step7">
                                <button type="button"
                                    class="btn btn-primary ">{{ labels('admin_labels.next', 'Next') }}</button>
                            </div>
                        </div>
                        <div class="card2 ml-2">
                            <div class="row">
                                <div class="col col-xxl-12">
                                    <div class="card p-5">
                                        <h6>{{ labels('admin_labels.product_description', 'Product Description') }}</h6>
                                        <hr>
                                        <div class="row mt-5">
                                            <div class="col-md-12">
                                                <label for=""
                                                    class="form-label">{{ labels('admin_labels.description', 'Description') }}</label>
                                                <textarea name="description" class="form-control addr_editor" placeholder="Place some text here">{{ isset($data->description) ? $data->description : '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-end ml-2 mt-xxl-3 mt-7 text-center">
                                <button type="submit"
                                    class="btn btn-primary submit_button">{{ labels('admin_labels.submit', 'Submit') }}</button>
                            </div>
                        </div>
                    </form>
                    <div class="float-end me-0 mt-3 px-3 row">
                        <p class="prev btn reset-btn">{{ labels('admin_labels.go_back', 'Go Back') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
