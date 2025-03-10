@php
    $bread_crumb['page_main_bread_crumb'] = labels(
        'front_messages.seller_register',
        'Seller Register - Telegram Verification',
    );
@endphp

<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <h1>Seller Registration - Step 1: Telegram Verification</h1>

        @if ($error_message)
            <div class="alert alert-danger">
                {{ $error_message }}
            </div>
        @else
            @if ($message)
                <div class="alert {{ $invite && $invite->status === 'active' ? 'alert-info' : 'alert-danger' }}">
                    {{ $message }}
                </div>
            @endif

            @if ($invite && $invite->status === 'active')
                <p>Please verify your Telegram account to proceed.</p>

                <div class="login-register pt-2">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                            <div class="inner h-100">
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <div class="form-row verify-telegram {{ $telegramVerified ? 'd-none' : '' }}">
                                            <label class="d-none" for="telegram_id">
                                                {{ labels('front_messages.validate_telegram_account', 'Validate your Telegram account') }}
                                                <span class="required">*</span>
                                            </label>
                                            <div class="telegram-login-button"></div>
                                        </div>
                                        @if ($telegramVerified)
                                            <div class="form-group col-12 verify-telegram-success">
                                                <p class="telegram-username">Telegram: @{{ $telegram_username }}</p>
                                            </div>
                                        @endif
                                        @error('telegram_id')
                                            <span class="error text-danger">{{ $message }}</span>
                                        @enderror
                                        <input type="hidden" wire:model="telegram_id" id="telegram_id">
                                        <input type="hidden" wire:model="telegram_username" id="telegram_username">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
    function onTelegramSellerRegister(user) {
        @this.call('verifyTelegram', user);
    }

    function initializeTelegramWidget() {
        const container = document.querySelector('.telegram-login-button');
        container.innerHTML = '';
        const script = document.createElement('script');
        script.src = "https://telegram.org/js/telegram-widget.js?22";
        script.async = true;
        script.setAttribute('data-telegram-login', "{{ $system_settings['tg_bot_user_name'] }}");
        script.setAttribute('data-size', 'large');
        script.setAttribute('data-userpic', 'true');
        script.setAttribute('data-radius', '6');
        script.setAttribute('data-onauth', 'onTelegramSellerRegister(user)');
        script.setAttribute('data-request-access', 'write');
        container.appendChild(script);
    }

    document.addEventListener('livewire:init', function() {
        console.log('Livewire initialized');
        initializeTelegramWidget();
    });

    window.addEventListener('reinit-telegram', function() {
        initializeTelegramWidget();
    });
</script>
