<div class="container">
    <div class="service-info row col-row row-cols-lg-4 row-cols-md-4 row-cols-sm-2 row-cols-2 text-center">
        @if ($web_settings['support_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">

                    <ion-icon class="icon p-3" name="call-outline"></ion-icon>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $web_settings['support_title'] }}</h3>
                    <span class="text-muted">{{ $web_settings['support_description'] }}</span>
                </div>
            </div>
        @endif
        @if ($web_settings['shipping_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">
                    <ion-icon class="icon p-3" name="cube-outline"></ion-icon>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $web_settings['shipping_title'] }}</h3>
                    <span class="text-muted">{{ $web_settings['shipping_description'] }}</span>
                </div>
            </div>
        @endif
        @if ($web_settings['safety_security_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">
                    <ion-icon class="icon p-3" name="card-outline"></ion-icon>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $web_settings['safety_security_title'] }}</h3>
                    <span class="text-muted">{{ $web_settings['safety_security_description'] }}</span>
                </div>
            </div>
        @endif
        @if ($web_settings['return_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">
                    <ion-icon class="icon p-3"s name="reload-outline"></ion-icon>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $web_settings['return_title'] }}</h3>
                    <span class="text-muted">{{ $web_settings['return_description'] }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
