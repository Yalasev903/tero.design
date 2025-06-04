<!doctype html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">

    <title>@yield('header_title', $page['title'] ?? 'Tero Design')</title>
    <meta name="description" content="@yield('header_description', $page['description'] ?? '')">
    <meta name="keywords" content="@yield('header_keywords', $page['keywords'] ?? '')">

    <link rel="shortlink" href="https://tero.design/"/>
    <link rel="icon" href="{{ asset('favicon.png') }}" sizes="32x32"/>
    <link rel="icon" href="{{ asset('favicon.png') }}" sizes="192x192"/>
    <link rel="apple-touch-icon-precomposed" href="{{ asset('favicon.png') }}"/>
    <meta name="msapplication-TileImage" content="{{ asset('favicon.png') }}"/>

    {{-- SEO OpenGraph --}}
    @yield('header_og_url_block')
    <meta property="og:site_name" content="Tero"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="@yield('header_og_type', 'website')"/>
    <meta property="og:title" content="@yield('header_meta_title', View::hasSection('header_title') ? trim($__env->yieldContent('header_title')) : ($page['title'] ?? 'Tero Design'))"/>
    <meta property="og:description" content="@yield('header_description', $page['description'] ?? '')"/>

    <meta name="twitter:card" content="summary_large_image"/>
    @section('header_og_image')
        <meta property="og:image" content="{{ asset('img/site_promo.jpg') }}"/>
        <meta property="og:image:secure_url" content="{{ asset('img/site_promo.jpg') }}"/>
        <meta property="og:image:width" content="1280"/>
        <meta property="og:image:height" content="768"/>
    @show

    {!! $base_config['googleTm'] ?? '' !!}

    @section('header_styles')
        <link rel="stylesheet" href="{{ asset('css/style.min.css?v=5.92') }}"/>
    @show
    <script src="{{ asset('js/header.min.js?v=5.92') }}" type="application/javascript"></script>
    @stack('styles')
    <style>
        .showreel {
            pointer-events: auto;
            position: fixed;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.02);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            overflow: auto;
            transition: none;
        }
        .showreel.open {
            display: flex !important;
        }
        .showreel-center {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            position: relative;
            padding-top: 0; /* Поднимает блок выше */
        }

        .showreel-poster-block {
            position: relative;
            width: 76vw;
            max-width: 1200px;
            min-width: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .showreel-poster-block img {
            width: 100%;
            max-width: 1200px;
            min-width: 340px;
            display: block;
            box-shadow: 0 2px 38px rgba(0,0,0,0.38);
            transition: box-shadow 0.2s;
        }
        .showreel-play {
            position: absolute;
            left: 50%; top: 44%;
            transform: translate(-50%, -50%);
            background: none; border: none; outline: none; cursor: pointer; z-index: 2;
        }
        .showreel-title {
            display: none;
            position: absolute;
            left: 50%; top: 64%;
            transform: translate(-50%, 0);
            color: #fff;
            font-size: 2.3rem;
            font-family: Montserrat,sans-serif;
            font-weight: 300;
            letter-spacing: .18em;
            text-shadow: 0 1px 18px #000;
            z-index: 2;
            pointer-events: none;
            text-align: center;
            width: 100%;
        }
        .showreel-player-video {
            width: 76vw;
            max-width: 1200px;
            min-width: 340px;
            background: #000;
            box-shadow: 0 2px 38px rgba(0,0,0,0.33);
            margin-top: 0;
            z-index: 3;
            display: block;
        }
        .showreel-close {
            position: absolute;
            top: 16px; right: 24px;
            z-index: 100;
            background: none; border: none; cursor: pointer; outline: none;
        }
        .showreel-close svg {
            width: 36px;
            height: 36px;
            filter: drop-shadow(0 2px 14px #000);
        }

        /* Мобильные адаптации */
        @media (max-width: 1250px) {
            .showreel-poster-block, .showreel-player-video {
                width: 96vw; max-width: 96vw; min-width: 0;
            }
            .showreel-title { font-size: 1.5rem; }
        }
        @media (max-width: 900px) {
            .showreel-poster-block, .showreel-player-video {
                width: 99vw; max-width: 99vw; min-width: 0;
            }
            .showreel-title { font-size: 1.15rem; }
        }
        @media (max-width: 700px) {
            .showreel-poster-block, .showreel-player-video {
                width: 100vw; max-width: 100vw; min-width: 0;
            }
            .showreel-title { font-size: 1.02rem; }
            .showreel-close svg { width: 28px; height: 28px; }
            .showreel-close { top: 12px; right: 16px; }
        }
        </style>
</head>
<body id="page" class="loading">
    <div class="loader">
        <video id="loading-video-banner" preload="metadata" playsinline inline muted autoplay>
            <source src="{{ asset('logo/loader.256.webm') }}" type="video/webm;">
            <source src="{{ asset('logo/loader.256.mp4') }}" type="video/mp4;">
            <img src="{{ asset('logo/loader.16.gif') }}">
        </video>
    </div>

    {{-- Меню --}}
    @php
        $menu_items = [
            [ 'title' => 'Services', 'link' => route('services') ],
            [ 'title' => 'Workflow', 'link' => route('workflow') ],
            [ 'title' => 'Contact',  'link' => route('contact') ],
        ];
    @endphp

    <div class="wrapper">
        <div class="wrap-header">
            <header class="header row2" id="js-header">
                <a href="/" class="header-logo row">
                    <video class="header-logo-img" preload="metadata" playsinline inline muted loop autoplay>
                        <source src="{{ asset('logo/small.256.webm') }}" type="video/webm;">
                        <source src="{{ asset('logo/small.256.mp4') }}" type="video/mp4;">
                        <img src="{{ asset('logo/small.16.gif') }}">
                    </video>
                </a>
                <div class="header-right row">
                    <nav class="header-nav row">
                        <div class="header-link js-showreel-open">Showreel</div>
                        @foreach ($menu_items as $item)
                            <a href="{{ $item['link'] }}" class="header-link">{{ $item['title'] }}</a>
                        @endforeach
                    </nav>
                    <div class="header-share"></div>
                </div>
                <a href="#" class="header-menu-trigger" id="js-menu-trigger"><span></span></a>
            </header>
        </div>

        @yield('content')

        <footer class="footer">
            <div class="footer-social row"></div>
            <div class="footer-copyright">All rights reserved © 2020 TERODESIGN</div>
        </footer>

        {{-- SHOWREEL MODAL --}}
        <div class="showreel" id="js-showreel">
            <div class="showreel-center">
                <div class="showreel-poster-block" id="showreel-poster-block">
                    <img src="/multimedia/showreel_2023/obl-2023_2.jpg"
                        id="showreel-poster-img"
                        alt="Showreel Poster">
                    <button type="button" class="showreel-play js-video-play">
                        <svg width="140" height="140" viewBox="0 0 140 140" fill="none">
                            <circle cx="70" cy="70" r="65" stroke="#fff" stroke-width="6"/>
                            <polygon points="58,45 105,70 58,95" fill="#fff"/>
                        </svg>
                    </button>
                    <div class="showreel-title">SHOWREEL</div>
                </div>
                <video id="js-video"
                    class="showreel-player-video"
                    preload="metadata"
                    controls
                    playsinline
                    muted
                    style="display:none;">
                    <source src="{{ asset('multimedia/showreel/Showreel_2024_HD.webm') }}" type="video/webm">
                    <source src="{{ asset('multimedia/showreel/Showreel_2024_HD.mp4') }}" type="video/mp4">
                    Ваш браузер не поддерживает видео.
                </video>
            </div>
            <a href="#" class="showreel-close" id="js-showreel-close">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" fill="#fff" width="48" height="48">
                    <path d="M574.9,500L977.7,97.3c18.1-18.1,16-49.6-4.7-70.2c-20.7-20.7-52.1-22.8-70.2-4.7L500,425.1L97.3,22.3C79.1,4.2,47.7,6.3,27,27C6.3,47.7,4.2,79.1,22.3,97.3L425.1,500L22.3,902.7C4.2,920.9,6.3,952.3,27,973c20.7,20.7,52.1,22.8,70.2,4.7L500,574.9l402.7,402.7c18.1,18.1,49.6,16,70.2-4.7c20.7-20.7,22.8-52.1,4.7-70.2L574.9,500L574.9,500z"/>
                </svg>
            </a>
        </div>
    </div> <!-- /.wrapper -->

    <div class="mobile-menu" id="js-mobile-menu">
        <ul class="mobile-menu-container">
            <li class="mobile-menu-item"><div class="mobile-menu-link js-showreel-open">Showreel</div></li>
            @foreach($menu_items as $item)
                <li class="mobile-menu-item"><a href="{{ $item['link'] }}" class="mobile-menu-link">{{ $item['title'] }}</a></li>
            @endforeach
        </ul>
    </div>

    @section('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="{{ asset('js/lightgallery.min.js') }}"></script>
        <script src="{{ asset('js/jquery.event.move.min.js') }}"></script>
        <script src="{{ asset('js/jquery.twentytwenty.min.js') }}"></script>
        <script src="{{ asset('js/main.min.js?v=5.92') }}"></script>
    @show

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('js-showreel');
            const playBtn = document.querySelector('.js-video-play');
            const closeBtn = document.getElementById('js-showreel-close');
            const video = document.getElementById('js-video');
            const posterBlock = document.getElementById('showreel-poster-block');
            const page = document.getElementById('page');

            const openModal = () => {
                modal.classList.add('open');
                modal.style.display = 'flex';

                // Скрываем скролл
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden'; // <- важно для Safari/Firefox

                if (page) page.classList.add('showreel-open'); // если нужно доп.стили

                // Обновляем видео
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                    video.style.display = 'none';
                }
                if (posterBlock) posterBlock.style.display = 'flex';
            };

            const closeModal = () => {
                modal.classList.remove('open');
                modal.style.display = 'none';

                // Возвращаем скролл
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';

                // Удаляем лишние классы
                if (page) {
                    page.classList.remove('loading', 'form-open', 'showreel-open');
                }

                // Останавливаем и скрываем видео
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                    video.style.display = 'none';
                }
                if (posterBlock) posterBlock.style.display = 'flex';
            };

            document.querySelectorAll('.js-showreel-open').forEach(el => {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    openModal();
                });
            });

            if (playBtn && video) {
                playBtn.addEventListener('click', function () {
                    posterBlock.style.display = 'none';
                    video.style.display = 'block';
                    video.play();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeModal();
                });
            }

            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });
        });
        </script>
    @stack('scripts')
</body>
</html>
