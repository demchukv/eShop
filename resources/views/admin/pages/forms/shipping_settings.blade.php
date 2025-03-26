@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.shipping_methods', 'Shipping Methods') }}
@endsection
@section('content')
    @php
        $user = auth()->user();
        $role = auth()->user()->role->name;
    @endphp
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.shipping_methods', 'Shipping Methods') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.optimize_and_manage_your_shipping_channels_with_ease', 'Optimize and Manage Your Shipping Channels with Ease') }}
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
                        <a href="{{ route('settings.index') }}">{{ labels('admin_labels.settings', 'Settings') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.shipping_methods', 'Shipping Methods') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <form id="" action="{{ route('shipping_settings.store') }}" class="submit_form"
                    enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="card-body">
                        <h5 class="mb-3">
                            {{ labels('admin_labels.shipping_methods', 'Shipping Methods') }}
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="form-group">
                                        <label class="mb-3"
                                            for="local_shipping_method">{{ labels('admin_labels.enable_local_shipping', 'Enable Local Shipping') }}
                                            <small>(Use Local Delivery
                                                Boy
                                                For Shipping)</small></label>
                                    </div>
                                    <div class="form-group card-body d-flex justify-content-end">
                                        <a class="toggle form-switch me-1 mb-1" title="Deactivate"
                                            href="javascript:void(0)">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                name="local_shipping_method"
                                                <?= @$settings['local_shipping_method'] == '1' ? 'checked' : '' ?>>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="mb-3"
                                        for="shiprocket_shipping_method">{{ labels('admin_labels.standard_delivery_method', 'Standard Delivery Method') }}
                                        (Shiprocket)
                                        <a href="https://app.shiprocket.in/api-user" target="_blank">Click here</a></small>
                                        to
                                        get credentials. <small><a href="https://www.shiprocket.in/" target="_blank">What is
                                                shiprocket?</a></small></label>
                                    <br>
                                    <div class="card-body d-flex justify-content-end">
                                        <a class="toggle form-switch me-1 mb-1" title="Deactivate"
                                            href="javascript:void(0)">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                name="shiprocket_shipping_method"
                                                <?= @$settings['shiprocket_shipping_method'] == '1' ? 'checked' : '' ?>>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label class="mb-3" for="email">{{ labels('admin_labels.email', 'Email') }}<span
                                        class="text-asterisks text-sm">*</span></label>
                                <input type="email" class="form-control" name="email" id="email"
                                    value="<?= isKeySetAndNotEmpty($settings, 'email') ? $settings['email'] : '' ?>"
                                    placeholder="Shiprocket account email" />
                            </div>
                            <div class="form-group">
                                <label class="mb-3" for="password">{{ labels('admin_labels.password', 'Password') }}<span
                                        class="text-asterisks text-sm">*</span></label>
                                <input type="password" class="form-control" name="password" id=""
                                    value="<?= isKeySetAndNotEmpty($settings, 'password') ? $settings['password'] : '' ?>"
                                    placeholder="Shiprocket account Password" />
                            </div>
                            <div class="form-group">
                                <label class="mb-3"
                                    for="webhook_url">{{ labels('admin_labels.enable_local_shipping', 'Enable Local Shipping') }}<span
                                        class="text-asterisks text-sm">*</span></label>
                                <input type="text" class="form-control" name="webhook_url" id=""
                                    value="<?= 'admin/webhook/spr_webhook' ?>" disabled />
                            </div>
                            <div class="form-group">
                                <label class="mb-3"
                                    for="webhook_token">{{ labels('admin_labels.shiprocket_webhook_token', 'Shiprocket Webhook Token') }}<span
                                        class="text-asterisks text-sm">*</span></label>
                                <input type="text" class="form-control" name="webhook_token" id=""
                                    value="<?= isKeySetAndNotEmpty($settings, 'webhook_token') ? $settings['webhook_token'] : '' ?>" />
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <span class="text-danger"><b>Note:</b> You can give free delivery charge only when
                                    <b>Standard delivery method</b> is enabled.</span>
                            </div>
                        </div>

                        <!-- Новий метод: Couriers List -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="mb-3" for="couriers_list_method">{{ 'Enable Couriers List' }}
                                        <small>(Manage a list of courier services)</small></label>
                                    <div class="card-body d-flex justify-content-end">
                                        <a class="toggle form-switch me-1 mb-1" title="Deactivate"
                                            href="javascript:void(0)">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                name="couriers_list_method"
                                                <?= @$settings['couriers_list_method'] == '1' ? 'checked' : '' ?>>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Поля для списку кур’єрів (показуються, якщо Couriers List увімкнено) -->
                        <div class="couriers-list-section"
                            style="display: <?= @$settings['couriers_list_method'] == '1' ? 'block' : 'none' ?>;">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>{{ 'Couriers List' }}</h6>
                                    <div id="couriers-list">
                                        @if (isset($settings['couriers_list']) && is_array($settings['couriers_list']))
                                            @foreach ($settings['couriers_list'] as $index => $courier)
                                                <div class="courier-entry row mb-2">
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control"
                                                            name="couriers_list[{{ $index }}][name]"
                                                            value="{{ $courier['name'] }}" placeholder="Courier Name"
                                                            required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control"
                                                            name="couriers_list[{{ $index }}][tracking_url]"
                                                            value="{{ $courier['tracking_url'] }}"
                                                            placeholder="Tracking URL (optional)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button"
                                                            class="btn btn-danger remove-courier">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary mt-2" id="add-courier">Add
                                        Courier</button>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="mb-3"
                                    for="shiprocket_shipping_method">{{ labels('admin_labels.enable_free_delivery', 'Enable Free Delivery') }}</label>
                                <div class="card-body d-flex justify-content-end">
                                    <a class="toggle form-switch me-1 mb-1" title="Deactivate" href="javascript:void(0)">
                                        <input type="checkbox" class="form-check-input" role="switch"
                                            name="standard_shipping_free_delivery"
                                            <?= @$settings['standard_shipping_free_delivery'] == '1' ? 'checked' : '' ?>>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="local_shipping_method">Minimum free delivery order amount </label>
                            <div>
                                <input type="number" min=1 class="form-control"
                                    name="minimum_free_delivery_order_amount" id=""
                                    value="<?= @$settings['minimum_free_delivery_order_amount'] ?>" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="reset"
                                class="btn mx-2 reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                            <button type="submit"
                                class="btn btn-primary submit_button">{{ labels('admin_labels.update_settings', 'Update Settings') }}</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Показати/сховати секцію Couriers List
            document.querySelector('input[name="couriers_list_method"]').addEventListener('change', function() {
                document.querySelector('.couriers-list-section').style.display = this.checked ? 'block' :
                    'none';
            });

            // Додати нового кур’єра
            document.getElementById('add-courier').addEventListener('click', function() {
                var index = document.querySelectorAll('#couriers-list .courier-entry').length;
                var html = `
                    <div class="courier-entry row mb-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="couriers_list[${index}][name]" placeholder="Courier Name" required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="couriers_list[${index}][tracking_url]" placeholder="Tracking URL (optional)">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-courier">Remove</button>
                        </div>
                    </div>`;
                document.getElementById('couriers-list').insertAdjacentHTML('beforeend', html);
            });

            // Видалити кур’єра
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-courier')) {
                    e.target.closest('.courier-entry').remove();
                }
            });
        });
    </script>
@endpush
