@php
$bread_crumb['page_main_bread_crumb'] = labels('front_messages.sign_in', 'Sign In');
@endphp
<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <div class="login-register pt-2">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                    <div class="inner h-100">
                        <h2 class="text-center fs-4 mb-3">
                            {{ labels('front_messages.sign_in', 'Sign In') }}
                        </h2>
                        <p class="text-center mb-4">
                            {{ labels('front_messages.if_you_have_an_account_with_us_please_log_in', 'If you have an account with us, please log in.') }}
                        </p>
                        <div class="form-row justify-content-around">
                            @if ($errors->has('loginError'))
                            <p class="fw-400 text-danger mt-1">{{ $errors->first('loginError') }}</p>
                            @endif
                            @if ($authentication_method == 'telegram')
                            <div class="form-group col-12">
                                <div class="form-row verify-telegram">
                                    <div class="form-group col-12 d-flex-justify-center mt-2">
                                        <script async src="https://telegram.org/js/telegram-widget.js?22"
                                            data-telegram-login="{{ $system_settings['tg_bot_user_name'] }}" data-size="large" data-userpic="false"
                                            data-radius="6" data-auth-url="login/telegram-get-user" data-request-access="write"></script>
                                        <p class="col-12 text-center mt-2"><a href="#" id="resetTelegramAuth">Login with other Telegram account</a></p>
                                    </div>
                                </div>
                                <div class="login-divide"><span
                                        class="login-divide-text">{{ labels('front_messages.or', 'OR') }}</span>

                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="login-signup-text mt-4 mb-2 fs-6 text-center text-muted">
                            {{ labels('front_messages.dont_have_an_account', 'Don,t have an account?') }}
                            <a href="{{ customUrl('register') }}" wire:navigate
                                class="btn-link">{{ labels('front_messages.sign_up_now', 'Sign up now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Main Content-->
</div>
