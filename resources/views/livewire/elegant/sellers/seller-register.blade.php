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
                                                        name="mobile" maxlength="16" {{-- oninput="validateNumberInput(this)"  --}}
                                                        class="form-control w-full" placeholder="8787878787"
                                                        value="{{ $mobile }}" />
                                                </div>
                                                @error('mobile')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
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
                                                <div class="input-group input-group-merge">

                                                    <input wire:model="password" type="password"
                                                        class="form-control show_seller_password" name="password"
                                                        placeholder="Enter Your Password" autocomplete="off">
                                                    <span class="input-group-text cursor-pointer toggle_password"><i
                                                            class="bx bx-hide"></i></span>
                                                </div>
                                                @error('password')
                                                    <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label"
                                                    for="password">{{ labels('admin_labels.confirm_password', 'Confirm Password') }}
                                                    <span class="text-asterisks text-sm">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input wire:model="confirm_password" type="password"
                                                        class="form-control" name="confirm_password"
                                                        placeholder="Enter your password" aria-describedby="password"
                                                        autocomplete="off" />
                                                    <span
                                                        class="input-group-text cursor-pointer toggle_confirm_password"><i
                                                            class="bx bx-hide"></i></span>
                                                </div>
                                                @error('confirm_password')
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
                                                    <x-filepond::upload wire:model="profile_image" max-files="5" />
                                                    {{-- <input wire:model="profile_image" type="file" class="filepond"
                                                        name="profile_image" id="profile_image"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        data-allow-drop="true" accept="image/*,.webp" />
                                                    @error('profile_image')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror --}}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="address_proof">{{ labels('admin_labels.address_proof', 'Address Proof') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <x-filepond::upload wire:model="address_proof" max-files="5" />
                                                    {{-- <input wire:model="address_proof" type="file" class="filepond"
                                                        name="address_proof" id="address_proof"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        data-allow-drop="true" accept="image/*,.webp" />
                                                    @error('address_proof')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror --}}

                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="authorized_signature">{{ labels('admin_labels.authorized_signature', 'Authorized Signature') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <x-filepond::upload wire:model="authorized_signature"
                                                        max-files="5" />
                                                    {{-- <input wire:model="authorized_signature" type="file"
                                                        class="filepond" name="authorized_signature"
                                                        id="authorized_signature" data-max-file-size="300MB"
                                                        data-allow-drop="true" data-max-files="200"
                                                        accept="image/*,.webp" />
                                                    @error('authorized_signature')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror --}}
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
                                                    <input type="file" class="filepond" name="store_logo"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        accept="image/*,.webp" />


                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-default-phone">{{ labels('admin_labels.store_thumbnail', 'Store Thumbnail') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <input type="file" class="filepond" name="store_thumbnail"
                                                        data-max-file-size="300MB" data-max-files="200"
                                                        accept="image/*,.webp" />

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-default-company">{{ labels('admin_labels.other_documents', 'Other Documents') }}</label>
                                                    {{-- <small>({{ $note_for_necessary_documents }})</small> --}}
                                                    <input type="file" class="filepond" name="other_documents[]"
                                                        multiple data-max-file-size="300MB" data-max-files="200" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-default-company">{{ labels('admin_labels.description', 'Description') }}
                                                        <span class="text-asterisks text-sm">*</span></label>
                                                    <textarea wire:model="description" id="basic-default-message" value="" name="description"
                                                        class="form-control" placeholder="Write some description here">{{ $description }}</textarea>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group city_list_parent">
                                                    <label for="city"
                                                        class="control-label mb-2 mt-2">{{ labels('admin_labels.city', 'City') }}
                                                        <span class='text-asterisks text-xs'>*</span></label>
                                                    <select wire:model="city" class="form-select city_list"
                                                        name="city" id="city">
                                                        <option value=" ">
                                                            {{ labels('admin_labels.select_city', 'Select City') }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city"
                                                        class="control-label mb-2 mt-2">{{ labels('admin_labels.zipcode', 'Zipcode') }}
                                                        <span class='text-asterisks text-xs'>*</span></label>
                                                    <select wire:model="zipcode" class="form-select zipcode_list"
                                                        name="zipcode" id="zipcode">
                                                        <option value=" ">
                                                            {{ labels('admin_labels.select_zipcode', 'Select Zipcode') }}
                                                        </option>
                                                    </select>
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
                                                        <input type="file" class="filepond"
                                                            name="national_identity_card" data-max-file-size="300MB"
                                                            data-max-files="200" accept="image/*,.webp" />
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
                        <button type="submit"
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
@endpush

@script
    <script>
        window.addEventListener("livewire:init", function() {
            console.log('Livewire initialized');
            Livewire.hook('message.sent', (message, component) => {
                console.log('Livewire message sent:', message);
            });

            Livewire.hook('message.processed', (message, component) => {
                console.log('Livewire message processed:', message);

            });

            Livewire.hook('element.updated', (el, component) => {
                console.log('Livewire element updated:', el);
            });


        });
        window.addEventListener('livewire:navigated', function() {
            console.log('Livewire navigated');
        });

        window.addEventListener('messages', (event) => {
            console.log('Livewire message sent:', event);
        });

        window.addEventListener('start-verification', (event) => {
            console.log('Start verification:', event);
        });

        window.addEventListener('load', function() {
            console.log('Window loaded');
            const mobile = document.querySelector("#mobile");
            window.intlTelInput(mobile, {
                hiddenInput: (telInputName) => ({
                    phone: "phone_full",
                    country: "country_code"
                }),
                allowExtensions: !0,
                formatOnDisplay: !0,
                autoFormat: !0,
                autoHideDialCode: !0,
                autoPlaceholder: !0,
                defaultCountry: "in",
                ipinfoToken: "yolo",
                nationalMode: !1,
                numberType: "MOBILE",
                preventInvalidNumbers: !0,
                separateDialCode: !0,
                initialCountry: "auto",
                geoIpLookup: callback => {
                    fetch("https://ipapi.co/json")
                        .then(res => res.json())
                        .then(data => callback(data.country_code))
                        .catch(() => callback("us"));
                },
                loadUtils: () => import(
                    "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/utils.js"
                )
            });

            const phoneFullInput = document.querySelector('input[name="phone_full"]');
            const countryCodeInput = document.querySelector('input[name="country_code"]');

            phoneFullInput.addEventListener('input', function() {
                @this.phone_full = phoneFullInput.value;
            });

            countryCodeInput.addEventListener('input', function() {
                @this.country_code = countryCodeInput.value;
            });

        });
    </script>
@endscript
