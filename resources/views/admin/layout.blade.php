<!DOCTYPE html>
<html lang="en">

<head>

    @include('admin.include_css')
    @stack('styles')
    @yield('styles')
</head>

<body>
    <div id="db-wrapper">

        <x-admin.side-bar />
        <div id="page-content">
            <x-admin.header />
            <div class="container-fluid mt-5 px-6" {{ session()->get('is_rtl') == 1 ? 'dir=rtl' : '' }}>
                @yield('content')
            </div>
        </div>
    </div>
    <x-admin.footer />
    <!-- Scripts -->
    @include('admin.include_script')
    @stack('scripts')
    @yield('scripts')
</body>

</html>
