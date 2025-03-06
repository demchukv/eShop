@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.categories_sliders', 'Categories Sliders') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.categories_sliders', 'Categories Sliders') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.dynamic_category_display_with_seamless_slider_management', 'Dynamic Category Display with Seamless Slider Management') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item second_breadcrumb_item">
                        {{ labels('admin_labels.categories', 'Categories') }}
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.categories_sliders', 'Categories Sliders') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">
                            {{ labels('admin_labels.update_slider', 'Update Category Slider') }}
                        </h5>
                        <div class="row">
                            <div class="form-group">
                                <form id="category-slider-form"
                                    action="{{ url('admin/category_sliders/update/' . $data->id) }}" class="submit_form"
                                    enctype="multipart/form-data" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="basic-default-fullname">{{ labels('admin_labels.title', 'Title') }}<span
                                                    class='text-asterisks text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="basic-default-fullname"
                                                placeholder="Popular Categories" name="title"
                                                value="{{ $data->title ?? '' }}">

                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="basic-default-fullname">{{ labels('admin_labels.select_category', 'Select Category') }}<span
                                                class='text-asterisks text-sm'>*</span></label>
                                        <select name="category_ids[]" required id="category_sliders_category"
                                            class="category_sliders_category w-100" multiple
                                            data-placeholder="Type to search and select categories">

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ in_array($category->id, explode(',', $data->category_ids ?? '')) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="color_picker"
                                                    class="d-block text-center">{{ labels('admin_labels.choose_background_color', 'Choose Background Color') }}<span
                                                        class='text-asterisks text-sm'>*</span></label>
                                                <input type="color" id="category_slider_color_picker"
                                                    oninput="updateColorCode('category_slider_color_picker')"
                                                    class="form-control d-block mx-auto"
                                                    value={{ isset($data->background_color) && !empty($data->background_color) ? $data->background_color : '' }}>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-4 mb-2">
                                            <div class="form-group">
                                                <input type="text" id="category_slider_color_picker_code"
                                                    oninput="updateColorPicker('category_slider_color_picker', this.value)"
                                                    value={{ !empty($data->background_color) ? $data->background_color : '' }}
                                                    name="background_color" class="form-control d-block mx-auto">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="category_style_select">
                                            {{ labels('admin_labels.select_style', 'Select Slider Style') }}
                                        </label>
                                        <select class="category_slider_style form-select form-control"
                                            name="category_slider_style">
                                            <option value="">
                                                {{ labels('admin_labels.select_style', 'Select Slider Style') }}
                                            </option>
                                            <option value="style_1" {{ $data->style === 'style_1' ? 'selected' : '' }}>
                                                Style 1</option>
                                            <option value="style_2" {{ $data->style === 'style_2' ? 'selected' : '' }}>
                                                Style 2</option>
                                        </select>
                                    </div>

                                    <div class="category_slider_style_images category_card_style_box">
                                        <img src="{{ getimageurl('system_images/category_slider_style_1.png') }}"
                                            class="style_1" alt="Category Slider Style 1" />
                                        <img src="{{ getimageurl('system_images/category_slider_style_2.png') }}"
                                            class="style_2" alt="Category Slider Style 2" />

                                    </div>

                                    <div class="row">
                                        <label for=""
                                            class="form-label">{{ labels('admin_labels.banner_image', 'Banner Image') }}<span
                                                class="text-asterisks text-sm">*</span></label>
                                        <div class="col-md-12">
                                            <div class="row form-group">
                                                <div class="col-md-6 file_upload_box border file_upload_border mt-2">
                                                    <div class="mt-2">
                                                        <div class="col-md-12  text-center">
                                                            <div>
                                                                <a class="media_link" data-input="banner_image"
                                                                    data-isremovable="0"
                                                                    data-is-multiple-uploads-allowed="0"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#media-upload-modal"
                                                                    value="Upload Photo">
                                                                    <h4><i class='bx bx-upload'></i> Upload
                                                                </a></h4>
                                                                <p class="image_recommendation">Recommended Size: 180 x 180
                                                                    pixels</p>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($category->banner && !empty($category->banner))
                                                    <label for="" class="text-danger mt-3">*Only Choose When Update is
                                                        necessary</label>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div
                                                            class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
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
                                                                value='{{ $data->banner_image }}'>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn mx-2 reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                                        <button type="submit"
                                            class="btn btn-primary submit_button">{{ labels('admin_labels.update_slider', 'Update Category Slider') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
