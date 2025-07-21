<!doctype html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <link rel="shortlink" href="{{ url('/') }}"/>
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}" sizes="32x32"/>
    <link rel="icon" href="{{ asset('favicon.png') }}" sizes="192x192"/>
    <link rel="apple-touch-icon-precomposed" href="{{ asset('favicon.png') }}"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&display=swap" rel="stylesheet">
    <meta name="msapplication-TileImage" content="{{ asset('favicon.png') }}"/>
    @vite(['resources/css/overrides.css'])
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/before-after.js/dist/before-after.min.css">
    @show
     <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
    <script src="{{ asset('js/header.min.js?v=5.92') }}" type="application/javascript"></script>



    @stack('styles')
    <style>
body {
  font-family: 'AvenirNextCyr-Light', sans-serif;
  font-size: 20px;
  line-height: 1;
}
.header-nav {
    margin-right: 16px !important;
}
.showreel {
    pointer-events: auto;
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    background-color: rgba(0, 0, 0, 0.72);
    overflow: hidden;
}
.showreel.open {
    display: flex !important;
}

/* Переопределение top */
.mobile-menu.open,
.showreel.open {
    top: 0px !important;
}

.showreel-center {
    position: relative;
    z-index: 2;
    width: 90vw;
    max-width: 1920px;
    padding: 30px 60px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Контейнер с постером и видео */
.showreel-poster-block,
.showreel-player-video {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #000;
    box-shadow: 0 2px 38px rgba(0, 0, 0, 0.38);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    max-height: calc(100vh - 100px);
}

/* Постер */
.showreel-poster-block img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Видео */
.showreel-player-video {
    display: none;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Кнопка Play */
.showreel-play {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background: none;
    border: none;
    outline: none;
    cursor: pointer;
    z-index: 3;
}

/* Заголовок */
.showreel-title {
    display: none;
    position: absolute;
    left: 50%;
    top: 64%;
    transform: translate(-50%, 0);
    color: #fff;
    font-size: 2.3rem;
    font-family: Montserrat, sans-serif;
    font-weight: 300;
    letter-spacing: .18em;
    text-shadow: 0 1px 18px #000;
    z-index: 3;
    pointer-events: none;
    text-align: center;
    width: 100%;
}

/* Кнопка закрытия (внутри .showreel-center) */
.showreel-close {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 10;
    background: none;
    border: none;
    cursor: pointer;
    outline: none;
}
.showreel-close svg {
    width: 36px;
    height: 36px;
    filter: drop-shadow(0 2px 14px #000);
}

/* Адаптивность */
@media (max-width: 1250px) {
    .showreel-center {
        width: calc(100vw - 60px);
        padding: 30px;
    }
    .showreel-title {
        font-size: 1.5rem;
    }
}
@media (max-width: 900px) {
    .showreel-center {
        width: calc(100vw - 40px);
        padding: 20px;
    }
    .showreel-title {
        font-size: 1.15rem;
    }
}
@media (max-width: 700px) {
    .showreel-center {
        width: calc(100vw - 20px);
        padding: 10px;
    }
    .showreel-title {
        font-size: 1.02rem;
    }
    .showreel-close svg {
        width: 28px;
        height: 28px;
    }
    .showreel-close {
        top: 12px;
        right: 12px;
    }
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
                    <video class="header-logo-img dark-logo" preload="metadata" playsinline inline muted loop autoplay>
                        <source src="{{ asset('logo/small.256.webm') }}" type="video/webm;">
                        <source src="{{ asset('logo/small.256.mp4') }}" type="video/mp4;">
                        <img src="{{ asset('logo/small.16.gif') }}">
                    </video>

                    <video class="header-logo-img light-logo" preload="metadata" playsinline inline muted loop autoplay>
                        <source src="{{ asset('logo/light-theme.webm') }}" type="video/webm;">
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

                        <div class="theme-toggle-wrapper">
                            <label class="switch">
                                <input type="checkbox" id="theme-toggle-checkbox">
                                <span class="slider round"></span>
                            </label>
                        </div>
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
        @if(!empty($setting?->col_behance))
            <a href="{{ $setting->col_behance }}" class="footer-social-link" target="_blank" aria-label="Behance">
                <span class="icon-wrapper icon-dark">@include('components.svg.behance')</span>
                <span class="icon-wrapper icon-light">@include('components.svg.behance-white')</span>
            </a>
        @endif

        @if(!empty($setting?->col_facebook))
            <a href="{{ $setting->col_facebook }}" class="footer-social-link" target="_blank" aria-label="Facebook">
                <span class="icon-wrapper icon-dark">@include('components.svg.facebook')</span>
                <span class="icon-wrapper icon-light">@include('components.svg.facebook-white')</span>
            </a>
        @endif

        @if(!empty($setting?->col_instagram))
            <a href="{{ $setting->col_instagram }}" class="footer-social-link" target="_blank" aria-label="Instagram">
                <span class="icon-wrapper icon-dark">@include('components.svg.instagram')</span>
                <span class="icon-wrapper icon-light">@include('components.svg.instagram-white')</span>
            </a>
        @endif

        @if(!empty($setting?->col_linkedin))
            <a href="{{ $setting->col_linkedin }}" class="footer-social-link" target="_blank" aria-label="LinkedIn">
                <span class="icon-wrapper icon-dark">@include('components.svg.linkedin')</span>
                <span class="icon-wrapper icon-light">@include('components.svg.linkedin-white')</span>
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
        <script src="https://cdn.jsdelivr.net/npm/beerslider/dist/BeerSlider.min.js"></script>
        <script>
        window.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.beer-slider').forEach(el => {
            new BeerSlider(el)
            })
        })
        </script>
    @show

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('theme-toggle-checkbox');
            const html = document.documentElement;

            // Сохраняем тему
            const saved = localStorage.getItem('theme');
            if (saved === 'light') {
                html.classList.add('light-theme');
                checkbox.checked = true;
            }

            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    html.classList.add('light-theme');
                    localStorage.setItem('theme', 'light');
                } else {
                    html.classList.remove('light-theme');
                    localStorage.setItem('theme', 'dark');
                }
            });
        });
        </script>
<script>
(function () {
    const loader = document.querySelector('.loader');
    const grid = document.getElementById('js-gallery');
    const loadingVideo = document.getElementById('loading-video-banner');

    const showItemsSequentially = (items, index = 0) => {
        if (index >= items.length) return;
        const item = items[index];
        item.classList.add('visible');

        setTimeout(() => {
            requestAnimationFrame(() => {
                showItemsSequentially(items, index + 1);
            });
        }, 80);
    };

    const revealGrid = () => {
        if (grid) {
            grid.style.opacity = '1';
            const items = grid.querySelectorAll('.grid-item');
            showItemsSequentially(items);
        }
    };

    const hideLoader = () => {
        loader.style.opacity = '0';
        loader.style.pointerEvents = 'none';
        setTimeout(() => loader.remove(), 600);
        document.body.classList.remove('loading');
    };

    const init = () => {
        revealGrid(); // 👈 Показываем сетку СРАЗУ

        if (loadingVideo && loadingVideo.readyState >= 3) {
            loadingVideo.addEventListener('ended', hideLoader);
            setTimeout(hideLoader, 3600); // запас на 1 сек
        } else {
            setTimeout(hideLoader, 1000);
        }
    };

    if (document.readyState === 'complete') {
        init();
    } else {
        window.addEventListener('load', init);
    }
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('js-showreel');
    if (!modal) return;

    const playBtn = document.querySelector('.js-showreel-video-play');
    const closeBtn = document.getElementById('js-showreel-close');
    const video = document.getElementById('js-video');
    const posterBlock = document.getElementById('showreel-poster-block');
    const posterImg = document.getElementById('showreel-poster-img');
    const page = document.getElementById('page');

    const openModal = () => {
        modal.classList.add('open');
        modal.style.display = 'flex';

        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';

        if (page) page.classList.add('showreel-open');
        if (posterBlock) posterBlock.style.display = 'flex';
    };

    const closeModal = () => {
        modal.classList.remove('open');
        modal.style.display = 'none';

        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';

        if (page) {
            page.classList.remove('loading', 'form-open', 'showreel-open');
        }

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
        playBtn.addEventListener('click', async function () {
            try {
                // Загружаем данные о видео
                const response = await fetch('/api/admin/showreel');
                const data = await response.json();
                const media = data.media;

                if (!media || media.type !== 'video') throw new Error('Неверный формат видео');

                // Устанавливаем постер
                const poster = media.poster ? `/multimedia/${media.poster}` : '/multimedia/showreel_2023/obl-2023_2.jpg';
                if (posterImg) {
                    posterImg.src = poster;
                }

                // Очищаем видео от старых source
                video.innerHTML = '';

                for (const link of media.links) {
                    const source = document.createElement('source');
                    source.src = `/multimedia/${link.link}`;
                    source.type = link.mime || 'video/mp4';
                    video.appendChild(source);
                }

                video.load();
                posterBlock.style.display = 'none';
                video.style.display = 'block';

                // Временно отключаем звук для мобилок
                video.muted = true;

                await video.play();

                // Включаем звук после начала
                setTimeout(() => {
                    video.muted = false;
                    video.volume = 1.0;
                }, 300);

            } catch (err) {
                console.warn('❌ Ошибка загрузки или воспроизведения Showreel:', err);
                alert('Ошибка воспроизведения видео.');
            }
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

        <script>
        document.addEventListener('error', function (e) {
            if (e.target.tagName === 'IMG' && e.target.classList.contains('js-grid-item-media')) {
                e.target.src = '/img/placeholder.png';
            }
        }, true);
        </script>
    @stack('scripts')
</body>
</html>
