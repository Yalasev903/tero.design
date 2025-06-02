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
    {{-- Картинка для соцсетей --}}
    @section('header_og_image')
        <meta property="og:image" content="{{ asset('img/site_promo.jpg') }}"/>
        <meta property="og:image:secure_url" content="{{ asset('img/site_promo.jpg') }}"/>
        <meta property="og:image:width" content="1280"/>
        <meta property="og:image:height" content="768"/>
    @show

    {{-- Cookiebot --}}
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="55b2e969-431a-4913-a370-6f3742650a2d" data-blockingmode="auto" type="text/javascript"></script>
    <script id="CookieDeclaration" src="https://consent.cookiebot.com/55b2e969-431a-4913-a370-6f3742650a2d/cd.js" type="text/javascript"></script>

    {{-- Yoast SEO, JSON-LD, и прочее можно вынести как @section('seo_schema') в дочерних шаблонах --}}

    {{-- Google Tag Manager, если он есть --}}
    {!! $base_config['googleTm'] ?? '' !!}

    {{-- Основные стили --}}
    @section('header_styles')
        <link rel="stylesheet" href="{{ asset('css/style.min.css?v=5.92') }}"/>
    @show

    <script src="{{ asset('js/header.min.js?v=5.92') }}" type="application/javascript"></script>
    {{-- Встроенные/дополнительные стили --}}
    @stack('styles')

    <style>
    #page > div.wrapper > div.contact > div:nth-child(3) > ul { display: none !important; }
    #page > div.wrapper > div.contact > div:nth-child(3) > div { color: gray !important; display: table !important; }
    #txtmeLivechatCloseBtn { right: 40px !important; left: auto !important; }
    .contact-left { padding: 46px 90px !important; }
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

    {{-- Меню (формируется либо из переменных, либо через include компонента Blade/Vue) --}}
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
                    <div class="header-share">
                        {{-- Тут оставляй SVG и логику соцсетей, как в старом проекте, с подстановками --}}
                        {{-- ... --}}
                    </div>
                </div>
                <a href="#" class="header-menu-trigger" id="js-menu-trigger"><span></span></a>
            </header>
        </div>

        {{-- Основной контент страницы --}}
        @yield('content')

        {{-- Футер и соцсети --}}
        <footer class="footer">
            <div class="footer-social row">
                {{-- ... аналогично меню, SVG и ссылки соцсетей --}}
            </div>
            <div class="footer-copyright">All rights reserved © 2020 TERODESIGN</div>
        </footer>

        {{-- Showreel и modals --}}

    </div> <!-- /.wrapper -->

    {{-- Мобильное меню --}}
    <div class="mobile-menu" id="js-mobile-menu">
        <ul class="mobile-menu-container">
            <li class="mobile-menu-item"><div class="mobile-menu-link js-showreel-open">Showreel</div></li>
            @foreach($menu_items as $item)
                <li class="mobile-menu-item"><a href="{{ $item['link'] }}" class="mobile-menu-link">{{ $item['title'] }}</a></li>
            @endforeach
        </ul>
    </div>

    {{-- Скрипты --}}
    @section('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="{{ asset('js/lightgallery.min.js') }}"></script>
        <script src="{{ asset('js/jquery.event.move.min.js') }}"></script>
        <script src="{{ asset('js/jquery.twentytwenty.min.js') }}"></script>
        <script src="{{ asset('js/main.min.js?v=5.92') }}"></script>
    @show
    @stack('scripts')
</body>
</html>
