@extends('admin/layout')
@section('title')
{{ labels('admin_labels.s3_storage_setting', 'S3 Storage Setting') }}
@endsection
@section('content')

<div class="d-flex row align-items-center">
    <div class="col-md-6 col-xl-6 page-info-title">
        <h3>{{ labels('admin_labels.s3_storage_setting', 'S3 Storage Setting') }}
        </h3>
        <p class="sub_title">
            {{ labels('admin_labels.ensure_seamless_media_storage_integration_with_advanced_s3_storage_settings', 'Ensure Seamless Media Storage Integration with Advanced S3 Storage Settings') }}
        </p>
    </div>
    <div class="col-md-6 col-xl-6 d-flex justify-content-end">
        <nav aria-label="breadcrumb" class="float-end">
            <ol class="breadcrumb">
                <i class='bx bx-home-smile'></i>
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                </li>
                <li class="breadcrumb-item second_breadcrumb_item">
                    <a href="{{ route('settings.index') }}">{{ labels('admin_labels.settings', 'Settings') }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ labels('admin_labels.s3_storage_setting', 'S3 Storage Setting') }}
                </li>
            </ol>
        </nav>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">
                    {{ labels('admin_labels.s3_storage_setting', 'S3 Storage Setting') }}
                </h5>
                <div class="row">
                    <div class="form-group">
                        <form id="" action="{{ route('s3StorageSetting.store') }}" class="submit_form" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="m-2">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label" for="basic-default-fullname">{{ labels('admin_labels.aws_access_key_id', 'AWS Access Key ID ') }}<span class='text-asterisks text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="basic-default-fullname" placeholder="" name="aws_access_key_id" value="<?= isKeySetAndNotEmpty($settings, 'aws_access_key_id') ? $settings['aws_access_key_id'] : '' ?>">

                                        </div>

                                        <div class="mb-3 col-md-12">
                                            <label class="form-label" for="basic-default-fullname">{{ labels('admin_labels.aws_secret_access_key', 'AWS Secret Access Key') }}<span class='text-asterisks text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="basic-default-fullname" placeholder="" name="aws_secret_access_key" value="<?= isKeySetAndNotEmpty($settings, 'aws_secret_access_key') ? $settings['aws_secret_access_key'] : '' ?>">

                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label" for="basic-default-fullname">{{ labels('admin_labels.aws_default_region', 'AWS Default Region') }}<span class='text-asterisks text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="basic-default-fullname" placeholder="" name="aws_default_region" value="<?= isKeySetAndNotEmpty($settings, 'aws_default_region') ? $settings['aws_default_region'] : '' ?>">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label" for="basic-default-fullname">{{ labels('admin_labels.aws_bucket', 'AWS Bucket') }}<span class='text-asterisks text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="basic-default-fullname" placeholder="" name="aws_bucket" value="<?= isKeySetAndNotEmpty($settings, 'aws_bucket') ? $settings['aws_bucket'] : '' ?>">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn mx-2 reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                                <button type="submit" class="btn btn-primary submit_button">{{ labels('admin_labels.update_settings', 'Update Settings') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
