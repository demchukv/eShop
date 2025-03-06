@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.categories', 'Categories') }}
@endsection
@section('content')
    @php
        $user = auth()->user();
        $role = auth()->user()->role->name;
    @endphp
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
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.categories', 'Categories') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-xl-4 col-md-12 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="tree_view_html">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-md-12">
                    <div class="card">
                        <form id="" action="{{ route('categories.store') }}" class="submit_form"
                            enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="card-body">
                                <h5 class="mb-3">
                                    {{ labels('admin_labels.add_category', 'Add Category') }}
                                </h5>
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="basic-default-fullname">
                                            {{ labels('admin_labels.name', 'Name') }}
                                            <span class='text-asterisks text-sm'>*</span>
                                        </label>
                                        <input type="text" class="form-control" id="basic-default-fullname"
                                            placeholder="pizza" name="name" value="{{ old('name') }}">

                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="basic-default-fullname">
                                            {{ labels('admin_labels.select_catgeory_for_sub_categories', 'Select Category for subCategory') }}
                                        </label>
                                        <select id="" name="parent_id" class="form-select">
                                            <option value="">
                                                {{ labels('admin_labels.select_category', 'Select Category') }}
                                            </option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <label for="image" class="mb-2">
                                            {{ labels('admin_labels.image', 'Image') }}
                                            <span class='text-asterisks text-sm'>*</span>
                                        </label>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md-6 file_upload_box border file_upload_border mt-2 mx-2">
                                                    <div class="mt-2">
                                                        <div class="col-md-12  text-center">
                                                            <div>
                                                                <a class="media_link" data-input="category_image"
                                                                    data-isremovable="0"
                                                                    data-is-multiple-uploads-allowed="0"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#media-upload-modal"
                                                                    value="Upload Photo">
                                                                    <h4><i class='bx bx-upload'></i> Upload
                                                                    </h4>
                                                                </a>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 container-fluid row image-upload-section">
                                                    <div
                                                        class="col-md-12 col-sm-12 p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="image" class="mb-2">
                                            {{ labels('admin_labels.banner', 'Banner') }}
                                            <span class='text-asterisks text-sm'>*</span>
                                        </label>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md-6 file_upload_box border file_upload_border mt-2">
                                                    <div class="mt-2">
                                                        <div class="col-md-12  text-center">
                                                            <div>
                                                                <a class="media_link" data-input="banner"
                                                                    data-isremovable="0"
                                                                    data-is-multiple-uploads-allowed="0"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#media-upload-modal"
                                                                    value="Upload Photo">
                                                                    <h4><i class='bx bx-upload'></i> Upload
                                                                </a></h4>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 container-fluid row image-upload-section">
                                                    <div
                                                        class="col-md-12 col-sm-12 p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn mt-4 reset-btn mx-2" id="">
                                        {{ labels('admin_labels.reset', 'Reset') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary mt-4 submit_button" id="">
                                        {{ labels('admin_labels.add_category', 'Add Category') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="col-md-12 main-content mt-4{{ $user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view categories') ? '' : 'd-none' }}">
        <section class="overview-data">
            <div class="card content-area p-4 ">
                <div class="row align-items-center d-flex heading mb-5">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>{{ labels('admin_labels.categories', 'Categories') }}</h4>
                            </div>


                            <div class="col-sm-12 d-flex justify-content-end mt-md-0 mt-sm-2">

                                <div class="input-group me-3 search-input-grp">
                                    <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                                    <input type="text" data-table="admin_category_table"
                                        class="form-control searchInput" placeholder="Search...">
                                    <span class="input-group-text">Search</span>
                                </div>
                                <a class="btn me-2" id="tableFilter" data-bs-toggle="offcanvas"
                                    data-bs-target="#columnFilterOffcanvas" data-table="admin_category_table"
                                    StatusFilter='true'><i class='bx bx-filter-alt'></i></a>
                                <a class="btn me-2" id="tableRefresh" data-table="admin_category_table"><i
                                        class='bx bx-refresh'></i></a>
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle export-btn" type="button"
                                        id="exportOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class='bx bx-download'></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportOptionsDropdown">
                                        <li><button class="dropdown-item" type="button" onclick="exportTableData('admin_category_table','csv')">CSV</button></li>
                                        <li><button class="dropdown-item" type="button" onclick="exportTableData('admin_category_table','json')">JSON</button></li>
                                        <li><button class="dropdown-item" type="button" onclick="exportTableData('admin_category_table','sql')">SQL</button></li>
                                        <li><button class="dropdown-item" type="button" onclick="exportTableData('admin_category_table','excel')">Excel</button></li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                 <!-- Buttons -->
                 <div class="d-flex justify-content-start mt-2">
                    <button type="button"
                        class="btn btn-outline-primary btn-sm delete_selected_data" data-table-id="admin_category_table"
                        data-delete-url="{{ route('categories.delete') }}">{{ labels('admin_labels.delete_selected', 'Delete Selected') }}</button>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pt-0">
                            <div class="card-body p-0 list_view_html" id="">
                                <div class="gaps-1-5x"></div>

                                <div class="table-responsive">
                                    <table id='admin_category_table' data-toggle="table"
                                        data-loading-template="loadingTemplate" data-url="{{ route('categories.list') }}"
                                        data-side-pagination="server" data-pagination="true" data-click-to-select="true"
                                        data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false"
                                        data-show-columns="false" data-show-refresh="false" data-trim-on-search="false"
                                        data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                                        data-toolbar="" data-show-export="false" data-maintain-selected="true"
                                        data-export-types='["txt","excel","pdf","csv"]'
                                        data-export-options='{
                                                "fileName": "categories-list",
                                                "ignoreColumn": ["action"]
                                            }'
                                        data-query-params="category_query_params">
                                        <thead>
                                            <tr>
                                                <th data-checkbox="true">
                                                    <input name="select_all" type="checkbox">
                                                </th>
                                                <th data-field="id" data-sortable="true" data-visible='true'>
                                                    {{ labels('admin_labels.id', 'ID') }}
                                                </th>
                                                <th data-field="name" data-disabled="1" data-sortable="false">
                                                    {{ labels('admin_labels.name', 'Name') }}
                                                </th>
                                                <th class="d-flex justify-content-center" data-field="image"
                                                    data-sortable="false">
                                                    {{ labels('admin_labels.image', 'Image') }}
                                                </th>
                                                <th data-field="banner" data-sortable="false">
                                                    {{ labels('admin_labels.banner', 'Banner Image') }}
                                                </th>
                                                <th data-field="status" data-sortable="false">
                                                    {{ labels('admin_labels.status', 'Status') }}
                                                </th>
                                                <th data-field="operate" data-sortable="false">
                                                    {{ labels('admin_labels.action', 'Action') }}
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div id="" class="d-none tree_view_html">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

