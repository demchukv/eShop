@extends('admin/layout')
@section('title')
   {{ labels('admin_labels.update_brand', 'Update Brand') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.update_brand', 'Update Brand') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.elevate_your_store_with_seamless_brand_management', 'Elevate Your Store with Seamless Brand Management') }}
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
                        {{ labels('admin_labels.update_brand', 'Update Brand') }}
                </ol>
            </nav>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">
                    {{ labels('admin_labels.update_brand', 'Update Brand') }}
                </h5>
                <div class="form-group">
                    <form action="{{ url('/admin/brands/update/' . $data->id) }}" enctype="multipart/form-data"
                        method="POST" class="submit_form">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"
                                    for="basic-default-fullname">{{ labels('admin_labels.name', 'Name') }}</label>
                                <input type="text" class="form-control" id="basic-default-fullname" placeholder="Gucci"
                                    name="name" value="{{ $data->name }}">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label
                                        for="image">{{ labels('admin_labels.image', 'Image') }}
                                        <span class='text-asterisks text-sm'>*</span></label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="file_upload_box border file_upload_border mt-4">
                                                    <div class="mt-2 text-center">
                                                        <a class="media_link" data-input="image" data-isremovable="0"
                                                            data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                            data-bs-target="#media-upload-modal" value="Upload Photo">
                                                            <h4><i class='bx bx-upload'></i> Upload</h4>
                                                        </a>
                                                        <p class="image_recommendation">Recommended Size: 180 x 180 pixels
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($data->image && !empty($data->image))
                                                <div class="col-md-6">
                                                    <label for="" class="text-danger">*Only Choose When Update is
                                                        necessary</label>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div
                                                            class="col-md-8 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                            <div class='image-upload-div'>
                                                                <img class="img-fluid mb-2"
                                                                    src="{{ route('admin.dynamic_image', [
                                                                    'url' => getMediaImageUrl($data->image),
                                                                    'width' => 120,
                                                                    'quality' => 90,
                                                                ]) }}"
                                                                    alt="Not Found">
                                                            </div>
                                                            <input type="hidden" name="image"
                                                                value='{{ $data->image }}'>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="mb-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-primary submit_button"
                                id="">{{ labels('admin_labels.update_brand', 'Update Brand') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
