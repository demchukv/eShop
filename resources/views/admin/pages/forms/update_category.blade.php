@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.update_category', 'Update Category') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.categories', 'Categories') }}</h3>
            <p class="sub_title">
                {{ labels('admin_labels.effortless_category_management_for_an_organized_ecommerce_universe', 'Effortless Category Management for an Organized E-commerce Universe') }}
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
                        {{ labels('admin_labels.categories', 'Categories') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">

                <div class="card-body">
                    <h5 class="mb-3">
                        {{ labels('admin_labels.update_category', 'Update Category') }}
                    </h5>
                    <div class="row">
                        <div class="form-group">
                            <form action="{{ url('/admin/categories/update/' . $data->id) }}" enctype="multipart/form-data"
                                method="POST" class="submit_form">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label"
                                            for="basic-default-fullname">{{ labels('admin_labels.name', 'Name') }}</label>
                                        <input type="text" class="form-control" id="basic-default-fullname"
                                            placeholder="pizza" name="name" value="{{ $data->name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"
                                            for="basic-default-fullname">{{ labels('admin_labels.select_catgeory_for_sub_categories', 'Select Category for subCategory') }}</label>
                                        <select id="" name="parent_id" class="form-select">
                                            <option value="">select a category</option>
                                            @foreach ($hierarchicalCategories as $category)
                                                <option value="{{ $category['id'] }}"
                                                    {{ old('parent_id', $data->parent_id) == $category['id'] ? 'selected' : '' }}>
                                                    {{ $category['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">


                                        <div class="form-group">
                                            <div class="col-md-6 file_upload_box border file_upload_border mt-2 mx-2">
                                                <div class="mt-2">
                                                    <div class="col-md-12  text-center">
                                                        <div>
                                                            <a class="media_link" data-input="category_image"
                                                                data-isremovable="0" data-is-multiple-uploads-allowed="0"
                                                                data-bs-toggle="modal" data-bs-target="#media-upload-modal"
                                                                value="Upload Photo">
                                                                <h4><i class='bx bx-upload'></i> Upload
                                                            </a></h4>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            @if ($data->image && !empty($data->image))
                                                <label for="" class="text-danger mt-3">*Only Choose When Update is
                                                    necessary</label>
                                                <div class="container-fluid row image-upload-section">
                                                    <div
                                                        class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                        <div class='image-upload-div'>
                                                            <img class="img-fluid mb-2"
                                                                src="{{ route('admin.dynamic_image', [
                                                                    'url' => getMediaImageUrl($data->image),
                                                                    'width' => 150,
                                                                    'quality' => 90,
                                                                ]) }}"
                                                                alt="Not Found">
                                                        </div>
                                                        <input type="hidden" name="category_image"
                                                            value='{{ $data->image }}'>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <div class="col-md-6 file_upload_box border file_upload_border mt-2">
                                                <div class="mt-2">
                                                    <div class="col-md-12  text-center">
                                                        <div>
                                                            <a class="media_link" data-input="banner" data-isremovable="0"
                                                                data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                                data-bs-target="#media-upload-modal" value="Upload Photo">
                                                                <h4><i class='bx bx-upload'></i> Upload
                                                            </a></h4>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            @if ($data->banner && !empty($data->banner))
                                                <label for="" class="text-danger mt-3">*Only Choose When Update is
                                                    necessary</label>
                                                <div class="container-fluid row image-upload-section">
                                                    <div
                                                        class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                        <div class='image-upload-div'>
                                                            <img class="img-fluid mb-2"
                                                                src="{{ route('admin.dynamic_image', [
                                                                    'url' => getMediaImageUrl($data->banner),
                                                                    'width' => 150,
                                                                    'quality' => 90,
                                                                ]) }}"
                                                                alt="Not Found">
                                                        </div>
                                                        <input type="hidden" name="banner" value='{{ $data->banner }}'>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary submit_button"
                                        id="">{{ labels('admin_labels.update_category', 'Update Category') }}</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
