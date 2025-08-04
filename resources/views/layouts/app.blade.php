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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
        <link rel="preload" as="style" href="{{ asset('css/style.min.css?v=5.92') }}" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{{ asset('css/style.min.css?v=5.92') }}"></noscript>

        <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/before-after.js/dist/before-after.min.css" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/before-after.js/dist/before-after.min.css"></noscript>
    @show
    	    <script src="{{ asset('js/header.min.js?v=5.92') }}" type="application/javascript" defer></script>

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
.showreel-poster-block {
    position: relative;
    width: 100%;
    background: #000;
    box-shadow: 0 2px 38px rgba(0, 0, 0, 0.38);
    overflow: hidden;
    max-height: calc(100vh - 100px);
}

.showreel-player-video {
    width: 100%;
    max-width: 1920px;
    margin: 0 auto;
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
    width: 100%;
    max-width: 1920px;
    margin: 0 auto;
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
#page::after {
    content: none !important;
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
        .showreel-close {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        z-index: 10000 !important;
    }
    .showreel-center {
        width: calc(100vw - 40px);
        padding: 20px;
    }
    .showreel-title {
        font-size: 1.15rem;
    }
}
@media (max-width: 768px) {
    .showreel-close {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        z-index: 10000 !important;
    }

    .showreel-close svg {
        width: 28px !important;
        height: 28px !important;
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

    {{-- Cookiebot session tracker (обязательный пиксель) --}}
    <img id="CookiebotSessionPixel"
         src="https://imgsct.cookiebot.com/1.gif?dgi=55b2e969-431a-4913-a370-6f3742650a2d"
         alt="Cookiebot session tracker icon loaded"
         data-cookieconsent="ignore"
         style="display: none;">

    {{-- Cookiebot Widget --}}
    <div dir="ltr" id="CookiebotWidget" lang="en">
        <button class="CookiebotWidget-logo" aria-label="Open CMP widget" lang="en">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <g fill-rule="evenodd">
                    <circle cx="12" cy="12" r="12"></circle>
                    <path d="M15.094 13.978c-1.146 0-1.946-.813-1.946-1.978s.8-1.978 1.946-1.978c1.145 0 1.945.813 1.945 1.978s-.8 1.978-1.945 1.978M9.07 10.022h3.883l-.094.09c-.537.515-.844 1.203-.844 1.888 0 1.738 1.294 3 3.079 3 1.786 0 3.082-1.262 3.082-3s-1.296-3-3.082-3H9.079C7.295 9 6 10.262 6 12s1.295 3 3.079 3h2.144v-1.022H9.07c-1.136 0-1.932-.813-1.937-1.978 0-1.146.815-1.978 1.937-1.978" fill="#000"></path>
                </g>
            </svg>
        </button>
        <div id="CookiebotWidget-widgetContent"></div>
    </div>
    <div id="CookiebotWidgetUnderlay"></div>

    <style id="CookiebotWidgetStylesheet">
    @keyframes CookiebotWidgetFadeIn{0%{opacity:0}to{opacity:1}}#CookiebotWidget{word-wrap:break-word;left:10px;bottom:10px;animation:CookiebotWidgetFadeIn .3s ease-in;background-color:#000000;border-radius:40px;box-shadow:0 4px 16px rgba(0,0,0,.15);font-family:Helvetica,Arial,sans-serif;line-height:1.5;min-height:48px;min-width:48px;opacity:0;pointer-events:none;position:fixed;transition:all .2s ease-in;word-break:break-word;z-index:2147483631}#CookiebotWidget,#CookiebotWidget *{background:transparent;box-sizing:border-box;color:#ffffff;font-size:15px;letter-spacing:.1px;margin:0;outline:0;padding:0}#CookiebotWidget *{font-family:inherit}#CookiebotWidget button,#CookiebotWidget li,#CookiebotWidget strong,#CookiebotWidget svg,#CookiebotWidget ul{border:none;cursor:inherit;font-weight:inherit;line-height:1.5}#CookiebotWidget:not(.CookiebotWidget-inactive){opacity:1;pointer-events:all;transition:opacity .3s ease-in,border-radius .2s ease-in}#CookiebotWidget.CookiebotWidget-open{overflow:hidden}#CookiebotWidget:not(.CookiebotWidget-open):hover{box-shadow:0 4px 18px rgba(0,0,0,.3)}#CookiebotWidget+#CookiebotWidgetUnderlay{background:#000;height:100vh;left:0;opacity:0;pointer-events:none;position:fixed;top:0;visibility:hidden;width:100vw;z-index:2147483630}#CookiebotWidget:not(.CookiebotWidget-open) .CookiebotWidget-logo{cursor:pointer}#CookiebotWidget .CookiebotWidget-logo{display:block;transition:opacity .3s,transform .3s}#CookiebotWidget .CookiebotWidget-logo svg{display:block;height:44px;transition:all .3s;width:44px}#CookiebotWidget:not(.CookiebotWidget-open) button.CookiebotWidget-logo svg{height:48px;transition:all 0s ease;transition-delay:.2s;width:48px}#CookiebotWidget .CookiebotWidget-logo svg circle{fill:#000000}#CookiebotWidget .CookiebotWidget-logo svg path{fill:#ffffff}#CookiebotWidget #CookiebotWidget-widgetContent{display:flex;max-height:0;max-width:0;overflow:hidden;transition:all .2s ease-in}#CookiebotWidget.CookiebotWidget-open #CookiebotWidget-widgetContent{max-height:1000px;max-width:1000px}#CookiebotWidget.CookiebotWidget-open .CookiebotWidget-contents{max-height:calc(100vh - 10px);min-height:360px}#CookiebotWidget :focus-visible,#CookiebotWidget:not(.CookiebotWidget-open) .Cookiebotwidget-logo{outline:2px solid #1032CF;outline-offset:1px}@media screen and (max-width:600px){#CookiebotWidget:not(.CookiebotWidget-inactive){bottom:10px;left:10px}#CookiebotWidget :focus-visible,#CookiebotWidget:not(.CookiebotWidget-open) .Cookiebotwidget-logo{outline:0}}@media screen and (min-width:601px){#CookiebotWidget+#CookiebotWidgetUnderlay{display:none}}
    </style>


        {{-- SHOWREEL MODAL --}}
    @include('components.showreel', ['vimeoLink' => $vimeoLink])
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
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}
        <script src="{{ asset('js/lightgallery.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
        {{-- <script src="{{ asset('js/jquery.event.move.min.js') }}"></script>
        <script src="{{ asset('js/jquery.twentytwenty.min.js') }}"></script>
        <script src="{{ asset('js/main.min.js?v=5.92') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/beerslider/dist/BeerSlider.min.js"></script> --}}
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
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('js-showreel');
    const closeBtn = document.getElementById('js-showreel-close');
    const page = document.getElementById('page');
    let vimeoPlayer = null;

    const openModal = () => {
        modal.classList.add('open');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        if (page) page.classList.add('showreel-open');

        // 📱 Закрываем мобильное меню
        window.mobileMenuClose?.();

        const iframe = document.getElementById('vimeo-player');
        if (!vimeoPlayer && iframe && window.Vimeo) {
            vimeoPlayer = new Vimeo.Player(iframe);
        }
    };

    const closeModal = () => {
        modal.classList.remove('open');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
        if (page) page.classList.remove('showreel-open');

        if (vimeoPlayer) {
            vimeoPlayer.pause().catch(() => {});
            vimeoPlayer.setCurrentTime(0).catch(() => {});
        }
    };

    document.querySelectorAll('.js-showreel-open').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            openModal();
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', e => {
            e.preventDefault();
            closeModal();
        });
    }

    modal.addEventListener('click', e => {
        if (e.target.classList.contains('showreel')) closeModal();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const menuTriggerElement = document.getElementById('js-menu-trigger');
	const mobileMenuElement = document.getElementById('js-mobile-menu');
	const headerElement = document.getElementById('js-header');

	if (!menuTriggerElement || !mobileMenuElement || !headerElement) {
		console.warn('[⚠️] Элементы мобильного меню не найдены');
		return;
	}

	menuTriggerElement.addEventListener('click', () => {
		menuTriggerElement.classList.toggle('open');
		mobileMenuElement.classList.toggle('open');
		headerElement.classList.toggle('fixed');
	});

	window.mobileMenuClose = function () {
		menuTriggerElement.classList.remove('open');
		mobileMenuElement.classList.remove('open');
		headerElement.classList.remove('fixed');
	};
});
</script>

<script>
  window.requestIdleCallback(() => {
    const s1 = document.createElement('script')
    s1.src = 'https://consent.cookiebot.com/uc.js'
    s1.dataset.cbid = '55b2e969-431a-4913-a370-6f3742650a2d'
    s1.dataset.blockingmode = 'auto'
    s1.type = 'text/javascript'
    document.head.appendChild(s1)

    const s2 = document.createElement('script')
    s2.src = 'https://consent.cookiebot.com/55b2e969-431a-4913-a370-6f3742650a2d/cd.js'
    s2.type = 'text/javascript'
    document.head.appendChild(s2)
  })
</script>

<script>
document.addEventListener('lazybeforeunveil', function(e){
    if (e.target.tagName === 'VIDEO') {
        const video = e.target
        const sources = video.querySelectorAll('source[data-src]')
        sources.forEach(source => {
            source.src = source.dataset.src
        })
        video.load()
    }
})
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
    @yield('scripts')
</body>
</html>
