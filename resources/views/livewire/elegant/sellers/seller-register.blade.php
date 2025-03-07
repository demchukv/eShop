@php
    $bread_crumb['page_main_bread_crumb'] = labels('front_messages.seller_register', 'Seller Register');
@endphp

<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <h1>Seller Registration</h1>

        @if ($message)
            <div class="alert {{ $invite && $invite->status === 'active' ? 'alert-info' : 'alert-danger' }}">
                {{ $message }}
            </div>
        @endif

        <p>Register as a seller using this invitation link: <strong>{{ $link }}</strong> from
            <strong>{{ $user_info->username }}</strong>
        </p>

        <div class="login-register pt-2">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                    <div class="inner h-100">


                        <div class="form-row verify-telegram">
                            <div class="form-group col-12 d-flex-justify-center mt-2">
                                <script async src="https://telegram.org/js/telegram-widget.js?22"
                                    data-telegram-login="{{ $system_settings['tg_bot_user_name'] }}" data-size="large" data-userpic="true"
                                    data-radius="6" data-onauth="onTelegramSellerRegister(user)" data-request-access="write"></script>

                            </div>
                        </div>
                        <div class="form-row verify-telegram-success d-none">
                            <div class="form-group col-12">
                                <p class="telegram-username">Telegram verification successful</p>
                            </div>
                        </div>

                        <form class="seller-register-form d-none" wire:submit="register">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" wire:model="telegram_id" id="telegram_id">
                            <input type="hidden" wire:model="telegram_username" id="telegram_username">
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="username" class="d-none">
                                        {{ labels('front_messages.username', 'Username') }} <span
                                            class="required">*</span>
                                    </label>
                                    <input type="text" wire:model="username" placeholder="Username" id="username"
                                        class="form-control" autocomplete="off" />
                                    @error('username')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="form-group col-12 d-flex flex-column">
                                    <label for="mobile" class="d-none">{{ labels('front_messages.mobile', 'mobile') }}
                                        <span class="required">*</span></label>
                                    <input type="number" wire:model="mobile" name="mobile" placeholder="mobile"
                                        id="number" value="" />
                                    @error('mobile')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                {{-- <div class="form-group col-12 d-flex flex-column">
                                    <label for="mobile" class="d-none">
                                        {{ labels('front_messages.mobile', 'Mobile') }} <span class="required">*</span>
                                    </label>
                                    <input type="number" wire:model="mobile" placeholder="Mobile" id="mobile"
                                        class="form-control" />
                                    @error('mobile')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div> --}}

                                <div class="form-group col-12">
                                    <label for="email" class="d-none">
                                        {{ labels('front_messages.email', 'Email') }} <span class="required">*</span>
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
                                    <input type="text" wire:model="last_name" placeholder="Last name" id="last_name"
                                        class="form-control" />
                                    @error('last_name')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group col-12">
                                    <label for="password" class="d-none">
                                        {{ labels('front_messages.password', 'Password') }} <span
                                            class="required">*</span>
                                    </label>
                                    <input type="password" wire:model="password" placeholder="Password" id="password"
                                        class="form-control" />
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
    </div>
</div>


{{-- <button wire:click="register" class="btn btn-primary">
            Register Now
        </button> --}}

<script>
    function onTelegramSellerRegister(user) {
        $.ajax({
            type: "post",
            url: appUrl + "register/check-telegram",
            data: {
                user,
                check_type: "register",
            },
            success: function(response) {
                if (response.error == true) {
                    iziToast.error({
                        message: response.message,
                        position: "topRight",
                    });
                } else {
                    iziToast.success({
                        message: response.message,
                        position: "topRight",
                    });
                    if (!response.user.username) {
                        // if not set username change to id
                        response.user.username = response.user.id;
                    }
                    $("#telegram_id").val(response.user.id);
                    $("#telegram_username").val(response.user.username);
                    $("#username").val(response.user.username);
                    $("#first_name").val(response.user.first_name);
                    $("#last_name").val(response.user.last_name);
                    $(".verify-telegram").addClass("d-none");
                    $(".verify-telegram-success").removeClass("d-none");
                    $(".telegram-username").text(
                        "Telegram: @" + response.user.username
                    );
                    $(".seller-register-form").removeClass("d-none");
                }
            },
            error: function(response) {
                iziToast.error({
                    message: "Error checking Telegram auth",
                    position: "topRight",
                });
            },
        });
    }
</script>
