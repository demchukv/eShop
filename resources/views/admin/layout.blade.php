<!DOCTYPE html>
<html lang="en">
<head>

@include('admin.include_css')
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
    @stack('scripts')
</body>
@include('admin.include_script')

</html>
