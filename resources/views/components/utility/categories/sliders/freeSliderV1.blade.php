<section class="free-slideshow">
    <div class="container-fluid">
        <div class="swiper free-mySwiper">
            <div class="swiper-wrapper">
                {{-- first slide --}}
                <div class="free-slide-one swiper-slide">
                    <div class="free-slide-one-left">
                        <a href="#" class="free-slide-button headphones-btn">View now</a>
                    </div>
                    <div class="free-slide-one-right">
                        <div class="free-slide-bg"></div>
                        <a href="#" class="free-slide-button mac-btn">View now</a>
                    </div>
                </div>
                {{-- second slide --}}
                <div class="free-slide-two swiper-slide">
                    <div class="free-slide-two-left">
                        <a href="#" class="free-slide-button laptop-btn">View now</a>
                    </div>
                    <div class="free-slide-two-right">
                        <a href="#" class="free-slide-button consoles-btn">View now</a>
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
        .swiper-pagination {
            z-index: 1000;
        }

        .free-slideshow {
            position: relative;
            background: linear-gradient(to bottom, #ffffff 4.4%, var(--bg-light) 4.4%, var(--bg-light) 88.68%, #ffffff 88.68%);
            overflow: hidden;
        }

        .free-slide-one,
        .free-slide-two {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 25px;
            height: 100%;
        }

        .free-slide-one-left {
            position: relative;
            width: 66.69%;
            aspect-ratio: 2142 / 1052;
            background-image: url('assets/img/slides/one-left.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .free-slide-one-right {
            position: relative;
            width: 32.53%;
            aspect-ratio: 1045 / 1052;
        }

        .free-slide-one-right .free-slide-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 120%;
            height: 100%;
            background-image: url('assets/img/slides/one-right.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;

        }

        /* second slide */
        .free-slide-two-left {
            position: relative;
            width: 23.53%;
            aspect-ratio: 751 / 953;
            background-image: url('assets/img/slides/two-left.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;
            overflow: hidden;
        }

        .free-slide-two-right {
            position: relative;
            width: 75.68%;
            aspect-ratio: 2415 / 1045;
            background-image: url('assets/img/slides/two-right.png');
            background-position: top left;
            background-size: contain;
            background-repeat: no-repeat;
        }

        /* buttons */
        .headphones-btn {
            display: block;
            position: absolute;
            /* left: calc(100vw / 3840 * 144); */
            /* top: calc(100vw / 3840 * 604); */
            left: calc((144 / 2142 * 100%));
            top: calc((604 / 920 * 100%));
        }

        .mac-btn {
            display: block;
            position: absolute;
            /* left: calc(100vw / 3840 * 113); */
            /* top: calc(100vw / 3840 * 604); */
            left: calc((113 / 1045 * 100%));
            top: calc((604 / 920 * 100%));
        }

        .consoles-btn {
            display: block;
            position: absolute;
            /* left: calc(100vw / 3840 * 79); */
            /* top: calc(100vw / 3840 * 559); */
            left: calc((79 / 2415 * 100%));
            top: calc((559 / 1045 * 100%));
        }

        .keyboard-btn {
            display: block;
            position: absolute;
            /* left: calc(100vw / 3840 * 968); */
            /* top: calc(100vw / 3840 * 920); */
            left: calc((968 / 2415 * 100%));
            top: calc((920 / 1045 * 100%));
        }

        .laptop-btn {
            display: block;
            position: absolute;
            /* left: calc(100vw / 3840 * 89); */
            /* top: calc(100vw / 3840 * 920); */
            left: calc((89 / 751 * 100%));
            top: calc((920 / 1045 * 100%));
        }


        .free-slide-button {
            background-color: var(--primary-color);
            border-radius: 10px;
            padding-top: calc(100vw / 3840 * 10);
            padding-bottom: calc(100vw / 3840 * 10);
            padding-left: calc(100vw / 3840 * 62);
            padding-right: calc(100vw / 3840 * 62);
            font-size: calc(100vw / 3840 * 30);
            color: #fff;
            text-transform: uppercase;
            text-align: center;
            font-weight: 800;
            text-wrap: nowrap;
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.5), 0 4px 4px 0 rgba(0, 0, 0, 0.25);
        }

        .free-slide-button:hover,
        .free-slide-button:active {
            background-color: var(--secondary-color);
            color: #fff;
        }




        /* Для екранів шириною 3840px і більше */
        @media (min-width: 3840px) {}

        /* Для екранів шириною до 2560px */
        @media (max-width: 2560px) {}

        /* Для екранів шириною до 1920px */
        @media (max-width: 1920px) {}

        /* Для екранів шириною до 1440px */
        @media (max-width: 1440px) {
            .free-slide-button {
                padding-top: calc(100vw / 3840 * 10);
                padding-bottom: calc(100vw / 3840 * 10);
                padding-left: calc(100vw / 3840 * 62);
                padding-right: calc(100vw / 3840 * 62);
                font-size: 18px;
                font-weight: 700;
            }
        }

        /* Для екранів шириною до 1024px */
        @media (max-width: 1024px) {
            .free-slide-button {
                border-radius: 8px;
                font-weight: 600;
                font-size: 15px;
            }


        }

        /* Для екранів шириною до 768px */
        @media (max-width: 768px) {
            .free-slide-button {
                border-radius: 6px;
                font-weight: 500;
                font-size: 13px;
            }

            .free-slide-one-left {
                width: 100%;
            }

            .free-slide-one-right {
                display: none;
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
            .free-slide-button {
                font-size: 10px;
            }
        }
    </style>
@endpush
