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
            <h3>{{ labels('admin_labels.system_registration', 'System Registration') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.register_system_from_here', 'Register System From here') }}
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
                        {{ labels('admin_labels.settings', 'Settings') }}
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.system_registration', 'System Registration') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">
                            {{ labels('admin_labels.system_registration', 'System Registration') }}
                        </h5>
                    </div>
                    <form id="" action="{{ route('admin.system_register') }}" class="submit_form"
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="card-body pt-0">
                            <div class="mb-3">
                                <label
                                    class="form-label">{{ labels('admin_labels.purchase_code', 'eShop Plus Purchase Code for app') }}<span
                                        class="text-asterisks text-sm">*</span></label>
                                <input type="text" class="form-control" id="purchase_code"
                                    placeholder="Enter your purchase code here" name="app_purchase_code" value="">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset"
                                    class="btn mx-2 reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                                <button type="submit"
                                    class="btn btn-primary submit_button">{{ labels('admin_labels.register', 'Register') }}</button>
                            </div>
                        </div>
                    </form>
                    <form id="" action="{{ route('admin.web_system_register') }}" class="submit_form"
                    enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <label
                                class="form-label">{{ labels('admin_labels.purchase_code', 'eShop Plus Purchase Code for web') }}<span
                                    class="text-asterisks text-sm">*</span></label>
                            <input type="text" class="form-control" id="purchase_code"
                                placeholder="Enter your purchase code here" name="web_purchase_code" value="">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="reset"
                                class="btn mx-2 reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                            <button type="submit"
                                class="btn btn-primary submit_button">{{ labels('admin_labels.register', 'Register') }}</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
