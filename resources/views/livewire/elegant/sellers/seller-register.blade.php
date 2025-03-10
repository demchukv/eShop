@php
    $bread_crumb['page_main_bread_crumb'] = labels(
        'front_messages.seller_register',
        'Seller Register - Complete Registration',
    );
@endphp

<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <h1>Seller Registration - Step 2: Complete Your Details</h1>

        @if ($message)
            <div class="alert {{ $invite && $invite->status === 'active' ? 'alert-info' : 'alert-danger' }}">
                {{ $message }}
            </div>
        @endif

        @if ($invite && $invite->status === 'active')
            <div>
                <form wire:submit.prevent='register' class="submit_form">
                    @csrf
                    {{-- <textarea cols="20" rows="20" id="cat_data" name="commission_data" class="image-upload-btn"></textarea> --}}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 col-xxl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">
                                            {{ labels('admin_labels.seller_details', 'Seller Details') }}
                                        </h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <p>&nbsp;</p>
                                                <label for="telegram_username"
                                                    class="form-label">{{ labels('admin_labels.telegram', 'Telegram: ') }}
                                                </label>
                                                <strong>{{ $telegram_username }}</strong>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="username"
                                                    class="form-label">{{ labels('admin_labels.username', 'Username') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input wire:model="username" class="form-control" type="text"
                                                        placeholder="johndoe" id="username" name="username"
                                                        value="{{ $telegram_username }}" autofocus />
                                                </div>
                                                @error('username')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="first_name"
                                                    class="form-label">{{ labels('admin_labels.first_name', 'First Name') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input wire:model="first_name" class="form-control" type="text"
                                                        placeholder="John" id="first_name" name="first_name"
                                                        value="{{ $first_name }}" />
                                                </div>
                                                @error('first_name')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="last_name"
                                                    class="form-label">{{ labels('admin_labels.last_name', 'Last Name') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input wire:model="last_name" class="form-control" type="text"
                                                        placeholder="Doe" id="last_name" name="last_name"
                                                        value="{{ $last_name }}" />
                                                </div>
                                                @error('last_name')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label class="form-label"
                                                    for="mobile">{{ labels('admin_labels.mobile', 'Mobile') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div wire:ignore class="input-group input-group-merge">
                                                    <input wire:model="mobile" type="text" id="mobile"
                                                        name="mobile" maxlength="16" class="form-control w-full"
                                                        value="{{ $mobile }}" />
                                                </div>
                                                @error('mobile')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                                @error('phone_full')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                                @error('country_code')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                                <input type="hidden" wire:model="country_code" name="country_code"
                                                    id="country_code" />
                                                <input type="hidden" wire:model="phone_full" name="phone_full"
                                                    id="phone_full" />
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label"
                                                    for="email">{{ labels('admin_labels.email', 'Email') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input wire:model="email" class="form-control"
                                                        placeholder="johndoe@gmail.com" type="email" name="email"
                                                        value="{{ $email }}">
                                                </div>
                                                @error('email')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6 form-password-toggle">
                                                <label class="form-label"
                                                    for="password">{{ labels('admin_labels.password', 'Password') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div wire:ignore class="input-group">
                                                    <input wire:model="password" type="password" class="form-control"
                                                        name="password" id="password"
                                                        placeholder="Enter Your Password" autocomplete="off">
                                                    <span
                                                        class="input-group-text cursor-pointer toggle_password"><ion-icon
                                                            name="eye-outline"></ion-icon></span>
                                                </div>
                                                @error('password')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label"
                                                    for="password">{{ labels('admin_labels.confirm_password', 'Confirm Password') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div wire:ignore class="input-group">
                                                    <input wire:model="password_confirmation" type="password"
                                                        class="form-control" name="password_confirmation"
                                                        id="password_confirmation" placeholder="Enter your password"
                                                        aria-describedby="password" autocomplete="off" />
                                                    <span
                                                        class="input-group-text cursor-pointer toggle_confirm_password"><ion-icon
                                                            name="eye-outline"></ion-icon></span>
                                                </div>
                                                @error('password_confirmation')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-6 col-md-12">
                                                <label class="form-label"
                                                    for="address">{{ labels('admin_labels.address', 'Address') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <textarea wire:model="address" name="address" class="form-control" placeholder="Write here your address">{{ $address }}</textarea>
                                                @error('address')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="profile_image">{{ labels('admin_labels.profile_image', 'Profile image') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <x-filepond::upload wire:model="profile_image" />
                                                    {{-- <input wire:model="profile_image" type="file" class="filepond"
                                                        name="profile_image" id="profile_image"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        data-allow-drop="true" accept="image/*,.webp" /> --}}
                                                    @error('profile_image')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="address_proof">{{ labels('admin_labels.address_proof', 'Address Proof') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <x-filepond::upload wire:model="address_proof" />
                                                    {{-- <input wire:model="address_proof" type="file" class="filepond"
                                                        name="address_proof" id="address_proof"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        data-allow-drop="true" accept="image/*,.webp" /> --}}
                                                    @error('address_proof')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror

                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="authorized_signature">{{ labels('admin_labels.authorized_signature', 'Authorized Signature') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <x-filepond::upload wire:model="authorized_signature" />
                                                    {{-- <input wire:model="authorized_signature" type="file"
                                                        class="filepond" name="authorized_signature"
                                                        id="authorized_signature" data-max-file-size="300MB"
                                                        data-allow-drop="true" data-max-files="200"
                                                        accept="image/*,.webp" /> --}}
                                                    @error('authorized_signature')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xxl-6 mt-md-2 mt-xxl-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">
                                            {{ labels('admin_labels.bank_details', 'Bank Details') }}
                                        </h5>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax_name"
                                                        class="col-sm-12 form-label">{{ labels('admin_labels.account_number', 'Account Number') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <div class="input-group input-group-merge">
                                                        <input wire:model="account_number" type="text"
                                                            class="form-control" id="account_number"
                                                            placeholder="Account Number" name="account_number"
                                                            value="{{ $account_number }}">
                                                    </div>
                                                    @error('account_number')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax_name"
                                                        class="col-sm-4 form-label">{{ labels('admin_labels.account_name', 'Account Name') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <div class="input-group input-group-merge">
                                                        <input wire:model="account_name" type="text"
                                                            class="form-control" id="account_name"
                                                            placeholder="Account Name" name="account_name"
                                                            value="{{ $account_name }}">
                                                    </div>
                                                    @error('account_name')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax_name"
                                                        class="col-sm-4 form-label">{{ labels('admin_labels.bank_name', 'Bank Name') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <div class="input-group input-group-merge">
                                                        <input wire:model="bank_name" type="text"
                                                            class="form-control" id="bank_name"
                                                            placeholder="Bank Name" name="bank_name"
                                                            value="{{ $bank_name }}">
                                                    </div>
                                                    @error('bank_name')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax_name"
                                                        class="col-sm-4 form-label">{{ labels('admin_labels.bank_code', 'Bank Code') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <div class="input-group input-group-merge">
                                                        <input wire:model="bank_code" type="text"
                                                            class="form-control" id="bank_code"
                                                            placeholder="Bank Code" name="bank_code"
                                                            value="{{ $bank_code }}">
                                                    </div>
                                                    @error('bank_code')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 col-xxl-6 mt-md-2 mt-xxl-0">
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <h5 class="mb-3">
                                            {{ labels('admin_labels.store_details', 'Store Details') }}
                                        </h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label"
                                                    for="store_name">{{ labels('admin_labels.store_name', 'Store Name') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input wire:model="store_name" type="text" name="store_name"
                                                        class="form-control" placeholder="starbucks"
                                                        value="{{ $store_name }}" />
                                                </div>
                                                @error('store_name')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label"
                                                    for="store_url">{{ labels('admin_labels.store_url', 'Store URL') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input wire:model="store_url" type="text" name="store_url"
                                                        class="form-control" placeholder="starbucks"
                                                        value="{{ $store_url }}" />
                                                </div>
                                                @error('store_url')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-default-phone">{{ labels('admin_labels.logo', 'Logo') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <x-filepond::upload wire:model="store_logo" />
                                                    {{-- <input type="file" class="filepond" name="store_logo"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        accept="image/*,.webp" /> --}}
                                                    @error('store_logo')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror

                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-default-phone">{{ labels('admin_labels.store_thumbnail', 'Store Thumbnail') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <x-filepond::upload wire:model="store_thumbnail" />
                                                    {{-- <input type="file" class="filepond" name="store_thumbnail"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        accept="image/*,.webp" /> --}}
                                                    @error('store_thumbnail')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-default-company">{{ labels('admin_labels.other_documents', 'Other Documents') }}</label>
                                                    {{-- <small>({{ $note_for_necessary_documents }})</small> --}}
                                                    <x-filepond::upload wire:model="other_document" multiple />
                                                    {{-- <input type="file" class="filepond" name="other_documents[]"
                                                        multiple data-max-file-size="300MB" data-max-files="200" /> --}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="basic-default-company">{{ labels('admin_labels.description', 'Description') }}
                                                            <span class="text-asterisks text-sm">*</span></label>
                                                        <textarea wire:model="description" id="basic-default-message" value="" name="description"
                                                            class="form-control" placeholder="Write some description here">{{ $description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city"
                                                        class="control-label mb-2 mt-2">{{ labels('front_messages.city', 'City') }}
                                                        <span class='text-asterisks text-xs'>*</span></label>
                                                    <input type="text" wire:model="city" class="form-control"
                                                        id="city" name="city">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="zipcode"
                                                        class="control-label mb-2 mt-2">{{ labels('admin_labels.zipcode', 'Zipcode') }}
                                                        <span class='text-asterisks text-xs'>*</span></label>
                                                    <input type="text" wire:model="zipcode" class="form-control"
                                                        id="zipcode" name="zipcode">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xxl-6 mt-md-2 mt-xxl-0">
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <h5 class="mb-3">
                                            {{ labels('admin_labels.other_details', 'Other Details') }}
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                        class="form-label">{{ labels('admin_labels.tax_name', 'Tax Name') }}</label>
                                                    <div>
                                                        <input wire:model="tax_name" type="text"
                                                            class="form-control" id="tax_name"
                                                            placeholder="Tax Name" name="tax_name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tax_number"
                                                        class="form-label">{{ labels('admin_labels.tax_number', 'Tax Number') }}
                                                    </label>
                                                    <div>
                                                        <input wire:model="tax_number" type="text"
                                                            class="form-control" id="tax_number"
                                                            placeholder="Tax Number" name="tax_number">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pan_number"
                                                        class="form-label">{{ labels('admin_labels.pan_number', 'Pan Number') }}</label>
                                                    <div>
                                                        <input wire:model="pan_number" type="text"
                                                            class="form-control" id="pan_number"
                                                            placeholder="Pan Number" name="pan_number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="latitude"
                                                        class="form-label">{{ labels('admin_labels.latitude', 'Latitude') }}</label>
                                                    <div>
                                                        <input wire:model="latitude" type="text"
                                                            class="form-control" id="latitude"
                                                            placeholder="Latitude" name="latitude">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="longitude"
                                                        class="form-label">{{ labels('admin_labels.longitude', 'Longitude') }}</label>
                                                    <div>
                                                        <input wire:model="longitude" type="text"
                                                            class="form-control" id="longitude"
                                                            placeholder="Longitude" name="longitude">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="national_identity_card"
                                                        class="form-label">{{ labels('admin_labels.national_identity_card', 'National Identity Card') }}
                                                        <span class='text-asterisks text-sm'>*</span></label>
                                                    <div>
                                                        <x-filepond::upload wire:model="national_identity_card"
                                                            accept="image/*,.webp" />
                                                        {{-- <input type="file" class="filepond"
                                                            name="national_identity_card" data-max-file-size="300MB"
                                                            data-max-files="200" accept="image/*,.webp" /> --}}
                                                        @error('national_identity_card')
                                                            <span class="error text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="btn btn-primary submit_button">{{ labels('admin_labels.add_seller', 'Send request') }}</button>
                    </div>
                </form>
            </div>
        @endif
    </div>

</div>
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css">
    <style>
        .filepond--credits {
            display: none;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/intlTelInput.min.js"></script>
    <script>
        window.addEventListener('show-error', function(event) {
            iziToast.error({
                message: event.detail.message,
                position: 'topRight'
            });
        });

        window.addEventListener('load', function() {
            let ipData;
            fetch("https://ipapi.co/json")
                .then(response => response.json())
                .then(data => {
                    ipData = data; // Зберігаємо дані в змінну
                    if (ipData && ipData.city && ipData.postal) {
                        @this.set('city', ipData.city);
                        @this.set('zipcode', ipData.postal);
                        @this.set('country_code', ipData.country_code); // Зберігаємо код країни
                    } else {
                        iziToast.error({
                            message: "Не вдалося отримати дані про місто та поштовий індекс.",
                            position: 'topRight'
                        });
                    }
                    const mobile = document.querySelector("#mobile");
                    const iti = window.intlTelInput(mobile, {
                        allowExtensions: true,
                        formatOnDisplay: true,
                        autoFormat: true,
                        autoHideDialCode: false,
                        autoPlaceholder: "polite",
                        defaultCountry: "in",
                        ipinfoToken: "yolo",
                        nationalMode: true,
                        numberType: "MOBILE",
                        preventInvalidNumbers: true,
                        separateDialCode: true,
                        initialCountry: "auto",
                        geoIpLookup: callback => {
                            if (ipData && ipData.country_code) {
                                callback(ipData.country_code);
                            } else {
                                callback("us"); // Якщо дані не доступні, повертаємо "us"
                            }
                        },
                        loadUtils: () => import(
                            "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/utils.js"
                        )
                    });
                });

            const handleChange = () => {
                if (mobile.value) {
                    if (iti.isValidNumber()) {
                        @this.set('phone_full', iti.getNumber().replace('+' + iti.getSelectedCountryData()
                            .dialCode, ''));
                        @this.set('country_code', iti.getSelectedCountryData().dialCode);
                    }
                }
            };
            mobile.addEventListener('change', handleChange);
            mobile.addEventListener('keyup', handleChange);

            const passwordBtn = document.querySelector(".toggle_password");
            const confirmPasswordBtn = document.querySelector(".toggle_confirm_password");
            passwordBtn.addEventListener('click', () => {
                togglePasswordVisibility('password');
            });
            confirmPasswordBtn.addEventListener('click', () => {
                togglePasswordVisibility('password_confirmation');
            });
            const togglePasswordVisibility = (id) => {
                const passwordInput = document.getElementById(id);
                const toggleButton = document.querySelector(`.toggle_${id}`);
                const isPasswordVisible = passwordInput.type === 'password';
                passwordInput.type = isPasswordVisible ? 'text' : 'password';
                toggleButton.innerHTML = isPasswordVisible ? '<ion-icon name="eye-off-outline"></ion-icon>' :
                    '<ion-icon name="eye-outline"></ion-icon>';
            };

        });
    </script>
@endpush
