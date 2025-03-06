@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.update_featured_section', 'Update Featured Sections') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.update_featured_section', 'Update Featured Sections') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.showcase_top_picks_with_effortless_featured_item_management', 'Showcase Top Picks with Effortless Featured Item Management') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.update_featured_section', 'Update Featured Sections') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <form class="form-horizontal form-submit-event submit_form" action="{{ route('feature_section.update', $data->id) }}"
        method="POST" id="" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-12 col-xxl-6">
                <div class="card">
                    <div class="card-body ">
                        <h5 class="mb-3">
                            {{ labels('admin_labels.update_featured_section', 'Update Featured Sections') }}
                        </h5>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="title"
                                    class="control-label mb-2 mt-2">{{ labels('admin_labels.title', 'Title') }}
                                    <span class='text-asterisks text-sm'>*</span></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="title"
                                        value="{{ isset($data->title) ? $data->title : '' }}" placeholder="Title">
                                </div>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="short_description"
                                class="control-label mb-2 mt-2">{{ labels('admin_labels.short_description', 'Short Description') }}
                                <span class='text-asterisks text-sm'>*</span></label>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="short_description"
                                    value="{{ isset($data->short_description) ? $data->short_description : '' }}"
                                    placeholder="Short description">
                            </div>
                        </div>
                        <div
                            class="form-group row select-categories {{ $data->product_type == 'custom_combo_products' ? 'd-none' : '' }}">
                            <label for="categories"
                                class="control-label mb-2 mt-2">{{ labels('admin_labels.categories', 'Categories') }}</label>
                            <div class="col-md-12">
                                <select name="categories[]" id="category_sliders_category"
                                    class="category_sliders_category w-100" multiple
                                    data-placeholder=" Type to search and select categories" onload="multiselect()">

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, explode(',', $data->categories ?? '')) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            @php
                                $product_type = [
                                    'new_added_products',
                                    'products_on_sale',
                                    'top_rated_products',
                                    'most_selling_products',
                                    'custom_products',
                                    'digital_product',
                                    'custom_combo_products',
                                ];
                            @endphp

                            <label for="product_type" class="control-label mb-2 mt-2">
                                {{ labels('admin_labels.product_type', 'Product Types') }}
                                <span class="text-danger text-sm">*</span>
                            </label>

                            <div class="col-md-12">
                                <select name="product_type" class="form-control form-select product_type">
                                    <option value=" ">Select Types</option>
                                    @foreach ($product_type as $row)
                                        <option value="{{ $row }}"
                                            {{ isset($data->id) && $data->product_type == $row ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $row)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <!-- for custom product -->

                        <div
                            class="form-group row custom_products {{ isset($data->id) && $data->product_type == 'custom_products' ? '' : 'd-none' }}">
                            <label for="product_ids"
                                class="control-label mb-2 mt-2">{{ labels('admin_labels.products', 'Products') }}
                                *</label>
                            <div class="col-md-12 search_admin_product_parent">
                                <select name="product_ids[]" class="search_admin_product w-100" multiple
                                    data-placeholder=" Type to search and select products" onload="multiselect()">
                                    @if (isset($data->id))
                                        @foreach ($product_details as $row)
                                            <option value="{{ $row['id'] }}" selected>{{ $row['name'] }}</option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>
                        <!-- for custom combo product -->

                        <div
                            class="form-group row custom_combo_products {{ isset($data->id) && $data->product_type == 'custom_combo_products' ? '' : 'd-none' }}">
                            <label for="combo_product_ids"
                                class="control-label mb-2 mt-2">{{ labels('admin_labels.combo_products', 'Combo Products') }}
                                *</label>
                            <div class="col-md-12">
                                <select name="combo_product_ids[]" class="search_admin_combo_product w-100" multiple
                                    data-placeholder=" Type to search and select products" onload="multiselect()">
                                    @if (isset($data->id))
                                        @foreach ($combo_product_details as $row)
                                            <option value="{{ $row['id'] }}" selected>{{ $row['title'] }}</option>
                                        @endforeach
                                    @endif


                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- for digital product -->
                        <div
                            class="form-group row digital_products {{ isset($data->id) && $data->product_type == 'digital_product' ? '' : 'd-none' }}">
                            <label for="digital_product_ids"
                                class="control-label mb-2 mt-2">{{ labels('admin_labels.digital_products', 'Digital Products') }}
                                *</label>
                            <div class="col-md-12">
                                <select name="digital_product_ids[]" class="search_admin_digital_product w-100" multiple
                                    data-placeholder=" Type to search and select products" onload="multiselect()">
                                    @if (isset($data->id))
                                        @foreach ($product_details as $row)
                                            <option value="{{ $row['id'] }}" selected>{{ $row['name'] }}</option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12 mt-2 mb-4">
                            <label for="image" class="mb-2">{{ labels('admin_labels.banner_image', 'Banner Image') }}
                                <span class='text-asterisks text-sm'>*</span>
                            </label>

                            <div class="col-md-12">
                                <div class="row form-group">
                                    <div class="col-md-6 file_upload_box border file_upload_border mt-2">
                                        <div class="mt-2">
                                            <div class="col-md-12  text-center">
                                                <div>
                                                    <a class="media_link" data-input="banner_image" data-isremovable="0"
                                                        data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                        data-bs-target="#media-upload-modal" value="Upload Photo">
                                                        <h4><i class='bx bx-upload'></i> Upload
                                                    </a></h4>
                                                    <p class="image_recommendation">Recommended Size: 1648 x 610 pixels</p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($data->banner_image && !empty($data->banner_image))
                                            <label for="" class="text-danger">*Only Choose When Update is
                                                necessary</label>
                                            <div class="container-fluid row image-upload-section">
                                                <div
                                                    class="col-md-6 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                    <div class='image-upload-div'>
                                                        <img class="img-fluid mb-2"
                                                            src="{{ route('admin.dynamic_image', [
                                                                'url' => getMediaImageUrl($data->banner_image),
                                                                'width' => 150,
                                                                'quality' => 90,
                                                            ]) }}"
                                                            alt="Not Found">
                                                    </div>
                                                    <input type="hidden" name="banner_image"
                                                        value="{{ $data->banner_image }}">
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-6 container-fluid row mt-3 image-upload-section">
                                                <div
                                                    class="col-md-12 col-sm-12 p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">

                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xxl-6 mt-md-2 mt-sm-2">
                <div class="card">
                    <div class="card-body ">
                        <h5 class="mb-3">
                            {{ labels('admin_labels.select_style', 'Select Style') }}
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color_picker"
                                        class="d-block">{{ labels('admin_labels.choose_background_color', 'Choose Background Color') }}</label>
                                    <input type="color" id="feature_section_color_picker"
                                        oninput="updateColorCode('feature_section_color_picker')"
                                        class="form-control d-block mx-auto"
                                        value={{ isset($data->background_color) && !empty($data->background_color) ? $data->background_color : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6 mt-4 mb-2">
                                <div class="form-group">
                                    <input type="text" id="feature_section_color_picker_code"
                                        oninput="updateColorPicker('feature_section_color_picker', this.value)"
                                        name="background_color" class="form-control d-block mx-auto"
                                        value={{ !empty($data->background_color) ? $data->background_color : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label" for="category_style_select">
                                        {{ labels('admin_labels.style', 'Select Section Header Style') }}
                                    </label>
                                    <select class="feature_section_header_style form-control form-select"
                                        name="header_style">
                                        <option value="header_style_1"
                                            {{ $data->header_style === 'header_style_1' ? 'selected' : '' }}>
                                            Style 1</option>
                                        <option value="header_style_2"
                                            {{ $data->header_style === 'header_style_2' ? 'selected' : '' }}>
                                            Style 2</option>
                                        <option value="header_style_3"
                                            {{ $data->header_style === 'header_style_3' ? 'selected' : '' }}>
                                            Style 3</option>
                                    </select>
                                </div>

                                <div class="feature_section_header_style_images feature_section_header_style_box">
                                    <img src="{{ getimageurl('system_images/feature_section_heading_style_1.png') }}"
                                        class="header_style_1" alt="Feature Section Heading Style 1" />
                                    <img src="{{ getimageurl('system_images/feature_section_heading_style_2.png') }}"
                                        class="header_style_2" alt="Feature Section Heading Style 2" />
                                    <img src="{{ getimageurl('system_images/feature_section_heading_style_3.png') }}"
                                        class="header_style_3" alt="Feature Section Heading Style 3" />

                                </div>
                            </div>

                            <div class="col-md-6 {{ $data->product_type == 'custom_combo_products' ? 'd-none' : '' }}">
                                <div class="mb-4">
                                    <label class="form-label" for="category_style_select">
                                        {{ labels('admin_labels.style', 'Select Section Style') }}
                                    </label>
                                    <select class="feature_section_style form-control form-select" name="style">
                                        <option value="style_1" {{ $data->style === 'style_1' ? 'selected' : '' }}> Style
                                            1
                                        </option>
                                        <option value="style_2" {{ $data->style === 'style_2' ? 'selected' : '' }}>Style 2
                                        </option>
                                        <option value="style_3" {{ $data->style === 'style_3' ? 'selected' : '' }}>Style 3
                                        </option>
                                    </select>
                                </div>

                                <div class="feature_section_style_images feature_section_style_box">
                                    <img src="{{ getimageurl('system_images/featured_section_style_1.png') }}"
                                        class="style_1" alt="Featured Section Style 1" />
                                    <img src="{{ getimageurl('system_images/featured_section_style_2.png') }}"
                                        class="style_2" alt="Featured Section Style 2" />
                                    <img src="{{ getimageurl('system_images/featured_section_style_3.png') }}"
                                        class="style_3" alt="Featured Section Style 3" />

                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit"
                                class="btn btn-primary submit_button">{{ labels('admin_labels.update_featured_section', 'Update Featured Sections') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
