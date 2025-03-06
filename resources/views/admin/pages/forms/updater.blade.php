@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.system_settings', 'System Settings') }}
@endsection
@section('content')
    @php
        $user = auth()->user();
        $role = auth()->user()->role->name;

    @endphp

    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.system_update', 'System Update') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.update_web_and_admin_panel_from_here', 'Update Web and Admin Panel From here') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item second_breadcrumb_item">
                        {{ labels('admin_labels.settings', 'Settings') }}
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.system_update', 'System Update') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="alert alert-primary alert-dismissible" role="alert">
            <?= labels('post_update_clear_browser_cache', 'Clear your browser cache by pressing CTRL+F5 after updating the system.') ?><button
                type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center"><span
                                class="badge bg-primary"><?= labels('current_version', 'Current version') . ' - ' ?>
                                {{ get_current_version() }}</span>
                        </div>
                        <form class="form-horizontal" id="system-update" action="{{ url('admin/settings/system-update') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="dropzone w-100 d-flex justify-content-center align-items-center" id="system-update-dropzone">

                                </div>
                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-primary"
                                        id="system_update_btn"><?= labels('update_the_system', 'Update the system') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
