<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (!file_exists($sqlDumpPath) && !file_exists($installViewPath))
        <meta name="keywords" content='{{ $metaKeys ?? $system_settings['app_name'] }}'>
        <meta name="description" content='{{ $metaDescription ?? $system_settings['app_name'] }}'>
        <meta name="product_image" property="og:image"
            content='{{ $metaImage ?? asset('storage/' . $web_settings['logo']) }}'>
        <link rel="shortcut icon" href="{{ asset('storage/' . $web_settings['favicon']) }}" type="image/x-icon">
        <title>{{ $title ?? '' }} {{ $system_settings['app_name'] }}</title>
    @endif
    <meta property="og:image:type" content="image/jpg,png,jpeg,gif,bmp,eps">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Livewire scripts -->
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/vendor/photoswipe.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/bootstrap-table.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/star-rating.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/star-rating.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/iziToast.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/shareon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/lightbox.css') }}">
    @livewireStyles
    @stack('styles')
</head>
@php
    $is_rtl = session('is_rtl') ?? 0;
@endphp

<body {{ $is_rtl == 1 ? 'dir=rtl' : '' }}>
    <div class="loading-state screen">
        <div class="loader">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
    <input type="hidden" id="user_id" name="user_id" value="{{ auth()->id() ?? '' }}">
    <input type="hidden" id="custom_url" name="custom_url" value="{{ url()->full() }}">
    <input type="hidden" id="current_url" name="current_url" value="{{ url()->current() }}">
    <input type="hidden" id="store_slug" name="store_slug" value="{{ session('store_slug') }}">
    <input type="hidden" id="current_store_id" name="current_store_id" value="{{ session('store_id') }}">
    <input type="hidden" id="default_store_slug" name="default_store_slug"
        value="{{ session('default_store_slug') }}">
    @if (!file_exists($sqlDumpPath) && !file_exists($installViewPath))
        @php
            $currency_code = session('currency') ?? $system_settings['currency_setting']['code'];
            $currency_details = fetchDetails('currencies', ['code' => $currency_code]);
            $currency_symbol = $currency_details[0]->symbol ?? $system_settings['currency_setting']['symbol'];
        @endphp
        <input type="hidden" id="currency" name="currency" value="{{ $currency_symbol }}">
        <livewire:header.header />
    @endif

    {{ $slot }}

    @if (!file_exists($sqlDumpPath) && !file_exists($installViewPath))
        <livewire:footer.footer />
    @endif

    <x-include-modal.modals />

    <script>
        function triggerLivewireNavigated() {
            const navigatedEvent = new Event('livewire:navigated');
            document.dispatchEvent(navigatedEvent);
        }
        document.addEventListener('DOMContentLoaded', () => {
            triggerLivewireNavigated();
            // console.log('Livewire navigated event triggered with DOM loaded');
        });
        document.addEventListener('livewire:navigated', () => {
            // Livewire сам викличе цю подію при wire:navigate
        });
    </script>

    @livewireScripts

    <script>
        window.isScriptsInitialized = window.isScriptsInitialized || false;

        if (!window.isScriptsInitialized) {
            window.isScriptsInitialized = true;

            const loadScript = (src, isModule = false) => {
                return new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = src;
                    if (isModule) script.type = 'module';
                    // Без defer, щоб скрипт виконувався одразу після завантаження
                    script.onload = () => {
                        resolve();
                    };
                    script.onerror = () => {
                        console.error(`Error loading script: ${src}`);
                        reject();
                    };
                    document.body.appendChild(script);
                });
            };

            (async () => {
                if (typeof jQuery === 'undefined') {
                    console.error('jQuery is not loaded');
                    return;
                }
                await loadScript("https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js");
                await loadScript("{{ asset('frontend/elegant/js/plugins.js') }}");
                // await loadScript("{{ asset('frontend/elegant/js/star-rating.js') }}"); // Раніше
                await loadScript("{{ asset('frontend/elegant/js/star-rating.min.js') }}"); // Раніше
                await loadScript("{{ asset('frontend/elegant/js/custom.js') }}"); // Раніше
                await loadScript("{{ asset('frontend/elegant/js/firebase-app.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/firebase-firestore.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/bootstrap-table.min.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/bootstrap-table-export.min.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/main.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/daterangepicker.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/intlTelInput.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/iziToast.min.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/select2.min.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/checkout.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/wallet.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/vendor/jquery.elevatezoom.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/moment.min.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/sweetalert2.all.min.js') }}");
                await loadScript("{{ asset('js/datepicker/js/bootstrap-datepicker.min.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/swiper-bundle.min.js') }}");
                await loadScript("{{ asset('frontend/elegant/js/shareon.iife.js') }}");
                await loadScript("https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js", true);

                window.dispatchEvent(new Event('scripts:loaded'));
                triggerLivewireNavigated();
            })();
        }
    </script>

    @filepondScripts

    @stack('scripts')

</body>

</html>
