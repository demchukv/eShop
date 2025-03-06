<!DOCTYPE html>
<html lang="en">

<head>
@include('seller.include_css')
</head>
<body>
    <div id="db-wrapper">
        <!-- navbar vertical -->
        <x-seller.side-bar />
        <!-- Page content -->
        <div id="page-content">
            <x-seller.header />
            <div class="container-fluid mt-5 px-6" {{ session()->get('is_rtl') == 1 ? 'dir=rtl' : '' }}>
                @yield('content')
            </div>
        </div>
    </div>
    <x-seller.footer />
    <!-- Scripts -->
    @include('seller.include_script')
</body>

</html>
