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
            <div class="login-register pt-2">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                        <div class="inner h-100">
                            <form class="seller-register-form" wire:submit.prevent="register">
                                <div class="form-row">
                                    <div class="form-group
                                        col-12">
                                        Telegram: {{ $telegram_username }}
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="username" class="d-none">
                                            {{ labels('front_messages.username', 'Username') }} <span
                                                class="required">*</span>
                                        </label>
                                        <input type="text" wire:model="username" placeholder="Username"
                                            id="username" class="form-control" autocomplete="off" />
                                        @error('username')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12 d-flex flex-column">
                                        <label for="mobile"
                                            class="d-none">{{ labels('front_messages.mobile', 'Mobile') }} <span
                                                class="required">*</span></label>
                                        <input type="number" wire:model="mobile" name="mobile" placeholder="Mobile"
                                            id="number" value="" />
                                        @error('mobile')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="email" class="d-none">
                                            {{ labels('front_messages.email', 'Email') }} <span
                                                class="required">*</span>
                                        </label>
                                        <input type="email" wire:model="email" placeholder="Email" id="email"
                                            class="form-control" />
                                        @error('email')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="first_name" class="d-none">
                                            {{ labels('front_messages.first_name', 'First name') }} <span
                                                class="required">*</span>
                                        </label>
                                        <input type="text" wire:model="first_name" placeholder="First name"
                                            id="first_name" class="form-control" />
                                        @error('first_name')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="last_name" class="d-none">
                                            {{ labels('front_messages.last_name', 'Last name') }} <span
                                                class="required">*</span>
                                        </label>
                                        <input type="text" wire:model="last_name" placeholder="Last name"
                                            id="last_name" class="form-control" />
                                        @error('last_name')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="password" class="d-none">
                                            {{ labels('front_messages.password', 'Password') }} <span
                                                class="required">*</span>
                                        </label>
                                        <input type="password" wire:model="password" placeholder="Password"
                                            id="password" class="form-control" />
                                        @error('password')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="password_confirmation" class="d-none">
                                            {{ labels('front_messages.confirm_password', 'Confirm Password') }} <span
                                                class="required">*</span>
                                        </label>
                                        <input type="password" wire:model="password_confirmation"
                                            placeholder="Confirm Password" id="password_confirmation"
                                            class="form-control" />
                                        @error('password_confirmation')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12">
                                        <div
                                            class="login-remember-forgot d-flex justify-content-between align-items-center">
                                            <div class="agree-check customCheckbox">
                                                <input id="agree" type="checkbox" wire:model="agree" />
                                                <label for="agree">
                                                    {{ labels('front_messages.i_agree_to_term_and_policy', 'I agree to terms & Policy.') }}
                                                </label>
                                            </div>
                                        </div>
                                        @error('agree')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12 mb-0">
                                        <button type="submit" class="btn btn-primary btn-lg w-100"
                                            wire:loading.attr="disabled">
                                            {{ labels('front_messages.register', 'Register') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', function() {
        console.log('Livewire initialized');
        let code = $("#number").intlTelInput("getSelectedCountryData").dialCode;
        console.log('Code:', code);
    });


    window.addEventListener('show-success', function(event) {
        console.log('Show success:', event.detail);
        iziToast.success({
            message: event.detail.message,
            position: 'topRight'
        });
    });

    window.addEventListener('show-error', function(event) {
        console.log('Show error:', event.detail);
        iziToast.error({
            message: event.detail.message,
            position: 'topRight'
        });
    });
</script>
