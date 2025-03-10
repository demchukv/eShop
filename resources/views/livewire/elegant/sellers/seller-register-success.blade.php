@php
    $bread_crumb['page_main_bread_crumb'] = labels('front_messages.registration_success', 'Registration Successful');
@endphp

<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <h1>Registration Successful!</h1>

        <div class="alert alert-success">
            <p>Welcome, <strong>{{ $username }}</strong>! Your seller account has been successfully registered.</p>
            <p>After verifying your store data, you will receive a notification.</p>
        </div>
        <p>You can now log in and start managing your store.</p>

        <div class="mt-4">
            <a href="{{ route('seller.home') }}" class="btn btn-primary">
                Go to Dashboard
            </a>
            <a href="{{ route('home') }}" class="btn btn-secondary ms-2">
                Return to Home
            </a>
        </div>
    </div>
</div>
