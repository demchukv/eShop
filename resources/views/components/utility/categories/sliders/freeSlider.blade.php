<section class="free-slideshow">
    <div class="container-fluid">
        <div class="swiper free-mySwiper">
            <div class="swiper-wrapper">
                {{-- first slide --}}
                <div class="free-slide-one swiper-slide">
                    <div class="free-slide-one-left">
                        <h2 class="free-slide-head">Headphones</h2>
                        <p class="free-slide-text">Crystal-clear sound with deep bass in every beat; Designed for
                            comfort — perfect for
                            work,
                            travel, or
                            play; Wireless freedom with long-lasting battery life; Don’t miss out — premium audio is
                            just a
                            click away.</p>
                        <a href="#" class="free-slide-button headphones-btn">View now</a>
                    </div>
                    <div class="free-slide-one-right">
                        <div class="free-slide-bg"></div>
                        <h2 class="free-slide-head">MAC</h2>
                        <p class="free-slide-text">Powerfull performance meets elegant design. Experience seamless
                            productivity with the MAC
                            -
                            built
                            for
                            creators and professionals.</p>
                        <a href="#" class="free-slide-button mac-btn">View now</a>
                    </div>
                </div>
                {{-- second slide --}}
                <div class="free-slide-two swiper-slide">
                    <div class="free-slide-two-left">
                        <div class="free-slide-two-left-text">
                            <h2 class="free-slide-head">Laptop</h2>

                            <p class="free-slide-text">Crystal-clear sound with deep bass in every beat; Designed for
                                comfort — perfect for work, travel, or play.</p>
                        </div>
                        <a href="#" class="free-slide-button laptop-btn">View now</a>
                    </div>
                    <div class="free-slide-two-right">
                        <div class="free-slide-two-left-text">
                            <h2 class="free-slide-head">Consoles</h2>
                            <p class="free-slide-text">Crystal-clear sound with deep bass in every beat; Designed for
                                comfort — perfect for work, travel, or play.</p>
                            <a href="#" class="free-slide-button">View now</a>
                        </div>
                        <div class="free-slide-two-right-sub">
                            <h2 class="free-slide-head">Keyboards</h2>
                            <p class="free-slide-text">Crystal-clear sound with deep bass in every beat.</p>
                        </div>
                        <a href="#" class="free-slide-button keyboard-btn">View now</a>
                    </div>
                </div>
            </div>
            {{-- dots pagination --}}
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>


@push('styles')
    <style>
        .free-slideshow {
            position: relative;
            background: linear-gradient(to bottom, #ffffff 4.4%, var(--bg-light) 4.4%, var(--bg-light) 88.68%, #ffffff 88.68%);
            overflow: hidden;
        }

        .free-slide-one,
        .free-slide-two {
            display: flex;
            justify-content: space-between;
            align-items: justify-between;
            gap: 25px;
        }

        .free-slide-one-left {
            position: relative;
            width: 66.69%;
            aspect-ratio: 2142 / 1052;
            padding-top: calc(100vw / 3840 * 322);
            padding-right: calc(100vw / 3840 * 599);
            padding-left: calc(100vw / 3840 * 144);

            background-image: url('assets/img/slides/slide-one-left.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .free-slide-head {
            color: var(--primary-color);
        }

        .free-slide-text {
            color: var(--text-slider);
        }

        .free-slide-one-right {
            position: relative;
            width: 32.53%;
            aspect-ratio: 1045 / 1052;
            padding-top: calc(100vw / 3840 * 183);
            padding-right: calc(100vw / 3840 * 114);
            padding-left: calc(100vw / 3840 * 342);
            color: #fff;
        }

        .free-slide-one-right .free-slide-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 120%;
            height: 100%;
            background-image: url('assets/img/slides/slide-one-right.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;

        }

        .free-slide-one-right .free-slide-head,
        .free-slide-two-left .free-slide-head {
            color: #fff;
        }

        .free-slide-one-right .free-slide-text,
        .free-slide-two-left .free-slide-text {
            color: #fff;
        }

        .free-slide-head {
            /* font-size: 70px; */
            font-size: calc(100vw / 3840 * 70);
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .free-slide-text {
            /* font-size: 30px; */
            font-size: calc(100vw / 3840 * 30);
            font-weight: 500;
            line-height: 1.25;
        }


        /* second slide */
        .free-slide-two-left {
            position: relative;
            width: 23.53%;
            aspect-ratio: 751 / 1045;
            max-width: 751px;
            padding-top: calc(100vw / 3840 * 600);
            padding-right: calc(100vw / 3840 * 89);
            padding-left: calc(100vw / 3840 * 89);
            background-image: url('assets/img/slides/slide-two-left.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;
            overflow: hidden;
        }

        .free-slide-two-right {
            position: relative;
            width: 75.68%;
            aspect-ratio: 2415 / 1045;
            padding-top: calc(100vw / 3840 * 267);
            padding-right: calc(100vw / 3840 * 1074);
            padding-left: calc(100vw / 3840 * 84);
            background-image: url('assets/img/slides/slide-two-right.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .free-slide-two-right-sub {
            position: absolute;
            left: 0;
            bottom: 0;
            padding-bottom: calc(100vw / 3840 * 125);
            padding-left: calc(100vw / 3840 * 615);
            padding-right: calc(100vw / 3840 * 1254);
        }

        .keyboard-btn {
            display: block;
            position: absolute;
            right: calc(100vw / 3840 * 1300);
            bottom: calc(100vw / 3840 * 59);
        }

        .laptop-btn {
            display: block;
            position: absolute;
            left: calc(100vw / 3840 * 89);
            bottom: calc(100vw / 3840 * 59);
        }

        .headphones-btn {
            display: block;
            position: absolute;
            left: calc(100vw / 3840 * 144);
            top: calc(100vw / 3840 * 604);
        }

        .mac-btn {
            display: block;
            position: absolute;
            left: calc(100vw / 3840 * 113);
            top: calc(100vw / 3840 * 604);
        }


        .free-slide-button {
            background-color: var(--primary-color);
            border-radius: 10px;
            /* padding: 10px 62px; */
            padding-top: calc(100vw / 3840 * 10);
            padding-bottom: calc(100vw / 3840 * 10);
            padding-left: calc(100vw / 3840 * 62);
            padding-right: calc(100vw / 3840 * 62);
            color: #fff;
            text-transform: uppercase;
            text-align: center;
            font-weight: 800;
            /* font-size: 30px; */
            font-size: calc(100vw / 3840 * 30);
            text-wrap: nowrap;
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.5), 0 4px 4px 0 rgba(0, 0, 0, 0.25);
        }

        .free-slide-button:hover,
        .free-slide-button:active {
            background-color: var(--secondary-color);
            color: #fff;
        }




        /* Для екранів шириною 3840px і більше */
        @media (min-width: 3840px) {
            .free-slide-head {
                margin-bottom: 10px;
            }

        }

        /* Для екранів шириною до 2560px */
        @media (max-width: 2560px) {
            .free-slide-head {
                margin-bottom: 10px;
            }
        }

        /* Для екранів шириною до 1920px */
        @media (max-width: 1920px) {
            .free-slide-head {
                margin-bottom: 8px;
            }
        }

        /* Для екранів шириною до 1440px */
        @media (max-width: 1440px) {
            .free-slide-head {
                margin-bottom: 10px;
            }
        }

        /* Для екранів шириною до 1024px */
        @media (max-width: 1024px) {
            .free-slide-head {
                margin-bottom: 6px;
            }

            .free-slide-text {
                line-height: 1;
            }

            .free-slide-button {
                border-radius: 8px;
                font-weight: 700;
            }


        }

        /* Для екранів шириною до 768px */
        @media (max-width: 768px) {
            .free-slide-head {
                margin-bottom: 8px;
            }

            .free-slide-button {
                border-radius: 6px;
                font-weight: 600;
            }

            .free-slide-two-left {
                display: none;
            }

            .free-slide-two-right {
                width: 100%;
            }
        }

        /* Для екранів шириною до 480px */
        @media (max-width: 480px) {
            .free-slide-head {
                margin-bottom: 4px;
            }
        }
    </style>
@endpush
