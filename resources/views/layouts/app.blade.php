<!doctype html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">

    {{-- SEO --}}
    @hasSection('header_title')
        <title>@yield('header_title')</title>
    @else
        <title>Tero Design</title>
    @endif

    @hasSection('header_description')
        <meta name="description" content="@yield('header_description')">
    @endif

    @hasSection('header_keywords')
        <meta name="keywords" content="@yield('header_keywords')">
    @endif

    <link rel="shortlink" href="https://tero.design/"/>
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}" sizes="32x32"/>
    <link rel="icon" href="{{ asset('favicon.png') }}" sizes="192x192"/>
    <link rel="apple-touch-icon-precomposed" href="{{ asset('favicon.png') }}"/>
    <meta name="msapplication-TileImage" content="{{ asset('favicon.png') }}"/>

    {{-- SEO OpenGraph --}}
    @yield('header_og_url_block')

    <meta property="og:site_name" content="Tero"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="@yield('header_og_type', 'website')"/>

    @hasSection('header_meta_title')
        <meta property="og:title" content="@yield('header_meta_title')"/>
    @else
        <meta property="og:title" content="@yield('header_title', 'Tero Design')"/>
    @endif

    @hasSection('header_description')
        <meta property="og:description" content="@yield('header_description')"/>
    @endif

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
            /* background: rgba(0, 0, 0, 0.02); */
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

        @php
            $setting = \App\Models\Setting::first();
        @endphp


        <footer class="footer">
            <div class="footer-social row">
                @if($setting->col_behance)
                <a href="{{ $setting->col_behance }}" class="footer-social-link" target="_blank">
                    @include('components.svg.behance')
                </a>
                @endif

                @if($setting->col_facebook)
                <a href="{{ $setting->col_facebook }}" class="footer-social-link" target="_blank">
                    @include('components.svg.facebook')
                </a>
                @endif

                @if($setting->col_instagram)
                <a href="{{ $setting->col_instagram }}" class="footer-social-link" target="_blank">
                    @include('components.svg.instagram')
                </a>
                @endif

                @if($setting->col_linkedin)
                <a href="{{ $setting->col_linkedin }}" class="footer-social-link" target="_blank">
                    @include('components.svg.linkedin')
                </a>
                @endif

            </div>

            <div class="footer-copyright">
                All rights reserved © {{ now()->year }} TERODESIGN
            </div>
        </footer>

        {{-- SHOWREEL MODAL --}}
    @include('components.showreel', ['showreel' => $showreel])
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
    document.addEventListener('DOMContentLoaded', async function () {
        const posterImg = document.getElementById('showreel-poster-img');
        const videoEl = document.getElementById('js-video');

        try {
            const response = await fetch('/api/admin/showreel');
            const data = await response.json();
            const media = data.media;

            if (!media || media.type !== 'video') return;

            const poster = media.poster ? `/multimedia/${media.poster}` : '/multimedia/showreel_2023/obl-2023_2.jpg';
            if (posterImg && poster) {
                posterImg.setAttribute('src', poster);
            }

            videoEl.innerHTML = ''; // очистим старые source'ы

            for (const link of media.links) {
                const source = document.createElement('source');
                source.src = `/multimedia/${link.link}`;
                source.type = link.mime || 'video/mp4';
                videoEl.appendChild(source);
            }

        } catch (error) {
            console.error('Ошибка загрузки Showreel:', error);
            posterImg.src = '/multimedia/showreel_2023/obl-2023_2.jpg';
        }
    });
    </script>
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
        @if (!empty($base_config['jivochat']) && $base_config['jivochat'] && filled($base_config['jivochat_id']))
        <script src="//code.jivosite.com/widget.js" data-jv-id="{{ $base_config['jivochat_id'] }}" async></script>
        @endif
    @stack('scripts')
</body>
</html>
