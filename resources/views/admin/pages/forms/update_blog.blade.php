@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.update_blog', 'Update Blog') }}
@endsection
@section('content')
    @php
        $user = auth()->user();
        $role = auth()->user()->role->name;
    @endphp

    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.update_blog', 'Update Blog') }}</h3>
            <p class="sub_title">
                {{ labels('admin_labels.craft_compelling_blogs_with_user_friendly_creation_tool', 'Craft Compelling Blogs with User-Friendly Creation Tool') }}
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
                        {{ labels('admin_labels.blogs', 'Blogs') }}</li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.manage_blogs', 'Manage Blogs') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Basic Layout -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">
                        {{ labels('admin_labels.update_blog', 'Update Blog') }}
                    </h5>
                    <div class="form-group">
                        <form id="" action="{{ url('admin/blogs/update/' . $data->id) }}" class="submit_form"
                            enctype="multipart/form-data" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="title"
                                                class="col-sm-2 form-label">{{ labels('admin_labels.title', 'Title') }}
                                                <span class='text-asterisks text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="title" placeholder="Title"
                                                name="title" value="{{ isset($data->title) ? $data->title : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label for="category"
                                                class="form-label">{{ labels('admin_labels.select_category', 'Select Category') }}
                                                <span class='text-asterisks text-sm'>*</span></label>
                                            <select name="category_id" class="form-select get_blog_category"
                                                data-placeholder="Search Categories">
                                                <option></option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $category->id == $data->category_id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label
                                                class="form-label">{{ labels('admin_labels.image', 'Image') }}<span
                                                    class="text-asterisks text-sm">*</span></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 file_upload_box border file_upload_border mt-2">
                                            <div class="mt-2">
                                                <div class="col-md-12  text-center">
                                                    <div>
                                                        <a class="media_link" data-input="image" data-isremovable="0"
                                                            data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                            data-bs-target="#media-upload-modal" value="Upload Photo">
                                                            <h4><i class='bx bx-upload'></i> Upload
                                                        </a></h4>
                                                        <p class="image_recommendation">Recommended Size : larger
                                                            than
                                                            400 x 260 &
                                                            smaller than 600 x 300 pixels.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="container-fluid row image-upload-section">
                                            <div
                                                class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                <div class='image-upload-div'><img class="img-fluid mb-2"
                                                        src="{{ route('admin.dynamic_image', [
                                                                    'url' => getMediaImageUrl($data->image),
                                                                    'width' => 150,
                                                                    'quality' => 90,
                                                                ]) }}" alt="Not Found">
                                                </div>
                                                <input type="hidden" name="image" value='<?= $data->image ?>'>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label
                                        class="form-label">{{ labels('admin_labels.description', 'Description') }}<span class='text-asterisks text-sm'>*</span></label>
                                    <textarea name="description" class="form-control textarea addr_editor" placeholder="Place some text here">{{ isset($data->description) ? $data->description : '' }}</textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm btn-primary mt-4 submit_button"
                                    id="">{{ labels('admin_labels.update_blog', 'Update Blog') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
