@extends('layouts.app')
@section('content')
    @section('header_title', $page['title'] ?? 'Tero Design')
    @section('header_description', $page['description'] ?? '')
    @section('header_keywords', $page['keywords'] ?? '')
    @section('header_meta_title', $page['title'] ?? 'Tero Design')
    <div class="grid" id="js-gallery" style="opacity: 0; transition: opacity 0.6s ease;">
        {{-- 🔁 Остальная сетка проектов --}}
@include('components.grid-rows', ['projects_grid' => $projects_grid])
    </div>

    <div id="scroll-to-top">
        {!! file_get_contents(resource_path('views/components/svg/up.svg')) !!}
    </div>

    {{-- Внутренние стили главной --}}
    <style>
        .popup { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 9999; text-align: center;}
        .popup img, .popup video { max-width: 100%; max-height: 100%; order: 1;}
        .popup .grid-item-title { color: white; font-size: 24px; margin-top: 10px;}
        .popup.active { display: flex; justify-content: center; align-items: center; flex-flow: column;}
        .popup a { order: 2; margin-top: 10px; margin-bottom: -10px;}
        .popup-inner { max-width: 80%; max-height: 80%; display: flex; align-items: center; justify-content: center; flex-flow: column; position: relative;}
        .popup .project-info { display: flex; flex-flow: row-reverse; align-items: center; justify-content: space-between; width: 100%; margin-bottom: 5px; margin-top: -5px;}
        .popup .i_svg { width: 32px; cursor: pointer;}
        .info-block { position: absolute; right: 16px; top: 34px; background: #0009; padding: 16px 16px; text-align: left; max-width: 330px;}
        .info-block .sub-title, .info-block span.bold, .info-block span.texteditor-inline-fontsize, .info-block p, .info-block ul li { font-size: 14px; line-height: 125%;}
        .info-block a { text-decoration: underline;}
        @media (max-width: 991px) { .info-block { top: 50px; }}
        @media (max-width: 576px) {
            .popup-inner { max-width: 95%; max-height: 95%;}
            .info-block ul li { font-size: 12px;}
            .info-block { right: 11px; top: 45px; background: #0009; padding: 8px 8px; text-align: left;}
        }
        .close_svg { position: absolute; right: 10px; top: 26px; width: 28px; cursor: pointer;}
        .project-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 5px;
        margin-top: -5px;
    }

    .project-info h3 {
        font-size: 18px;
        color: #fff;
        margin: 0;
        flex: 1;
    }

    .project-link-text {
    text-decoration: none;
    font-size: 14px;
    padding: 4px 10px;
    border-radius: 4px;
    transition: all 0.2s ease;
    }

    .project-link-text:hover {
        background: #000;
        color: #ccc;
    }

/* Повышаем специфичность и применяем !important */
body #scroll-to-top {
    position: fixed !important;
    bottom: 30px !important;
    right: 30px !important;
    cursor: pointer !important;
    z-index: 9999 !important;
    display: none !important;
    transition: opacity 0.3s ease !important;
    background-color: #ccc !important; /* серый фон */
    border-radius: 50% !important;
    width: 60px !important;
    height: 60px !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    box-shadow: 0 0 8px #0005 !important;
}

body #scroll-to-top img {
    width: 30px !important;
    height: 30px !important;
    filter: drop-shadow(0 0 2px #000a) !important;
    transition: filter 0.2s ease !important;
}

body #scroll-to-top:hover img {
    filter: brightness(10) drop-shadow(0 0 2px #000a) !important;
}
    </style>
    {{-- / Внутренние стили главной --}}

    {{-- JS для popup --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gridItemLinks = document.getElementsByClassName('grid-item');
    const popup = document.createElement('div');
    popup.classList.add('popup');
    document.body.appendChild(popup);

    for (let i = 0; i < gridItemLinks.length; i++) {
        gridItemLinks[i].addEventListener('click', function (e) {
            e.preventDefault();

            const mediaType = this.classList.contains('grid-item-video') ? 'video' : 'image';
            let mediaUrl = this.dataset.image;
            const title = this.querySelector('.grid-item-title').innerText;
            const projectLink = this.dataset.projectLink;
            const text2 = this.dataset.text2;

            const popupInner = document.createElement('div');
            popupInner.classList.add('popup-inner');
            popupInner.style.position = 'relative';

            // 🎥 Медиа
            if (mediaType === 'image') {
                const img = document.createElement('img');
                img.src = mediaUrl;
                popupInner.appendChild(img);
            } else {
                mediaUrl = this.dataset.video;
                const video = document.createElement('video');
                video.preload = 'metadata';
                video.muted = true;
                video.loop = true;
                video.autoplay = true;
                const source = document.createElement('source');
                source.src = mediaUrl;
                video.appendChild(source);
                popupInner.appendChild(video);
            }

            // 🔧 Верхняя строка: заголовок + кнопки
            const topBar = document.createElement('div');
            topBar.style.position = 'absolute';
            topBar.style.top = '-35px';
            topBar.style.left = '10px';
            topBar.style.right = '10px';
            topBar.style.zIndex = '2';
            topBar.style.height = '30px'; // для контроля высоты

            // Заголовок проекта — теперь абсолютно слева
            const titleElement = document.createElement('h3');
            titleElement.innerText = title;
            titleElement.style.color = '#fff';
            titleElement.style.margin = '0';
            titleElement.style.fontSize = '20px';
            titleElement.style.position = 'absolute';
            titleElement.style.left = '0';
            titleElement.style.top = '0';

            // Контейнер кнопок справа
            const buttonContainer = document.createElement('div');
            buttonContainer.style.position = 'absolute';
            buttonContainer.style.right = '0';
            buttonContainer.style.top = '-5px';

            // 🔗 Кнопка "View Full Project"
            if (projectLink) {
                const link = document.createElement('a');
                link.href = projectLink;
                link.target = '_blank';
                link.rel = 'noopener';
                link.classList.add('project-link-text');
                link.textContent = 'View Full Project';
                link.style.position = 'relative';
                link.style.top = '-12px';
                link.style.marginRight = '10px';
                buttonContainer.appendChild(link);
            }

            // ℹ️ Кнопка info
            const infoIcon = document.createElement('span');
            infoIcon.innerHTML = '<img class="i_svg" src="/multimedia/info.svg" alt="i">';
            infoIcon.classList.add('info-button');
            infoIcon.style.cursor = 'pointer';

            infoIcon.addEventListener('click', function () {
                let infoBlock = popupInner.querySelector('.info-block');
                if (infoBlock) {
                    popupInner.removeChild(infoBlock);
                } else {
                    infoBlock = document.createElement('div');
                    infoBlock.classList.add('info-block');
                    infoBlock.innerHTML = text2;
                    popupInner.appendChild(infoBlock);
                }
            });

            buttonContainer.appendChild(infoIcon);

            topBar.appendChild(titleElement);
            topBar.appendChild(buttonContainer);
            popupInner.appendChild(topBar);

            // Отобразить popup
            popup.innerHTML = '';
            popup.appendChild(popupInner);
            popup.classList.add('active');

            const mediaEl = popupInner.querySelector('img, video');
            mediaEl?.addEventListener('click', function (e) {
                if (e.target === this) {
                    popup.innerHTML = '';
                    popup.classList.remove('active');
                }
            });
        });
    }

    popup.addEventListener('click', function (e) {
        if (e.target === this) {
            this.innerHTML = '';
            this.classList.remove('active');
        }
    });
});
</script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const scrollBtn = document.getElementById('scroll-to-top');

        // Появление кнопки при скролле вниз
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                scrollBtn.style.display = 'block';
            } else {
                scrollBtn.style.display = 'none';
            }
        });

        // Скролл наверх по клику
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>
    @section('scripts')
    {{-- ... все скрипты ... --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/plugins/ls.attrchange.min.js" async></script>

    {{-- 👇 Добавить вот этот блок --}}
    <script>
        document.addEventListener('lazyloaded', function (e) {
            const el = e.target;

            if (el.tagName === 'VIDEO') {
                el.querySelectorAll('source').forEach(source => {
                    if (source.dataset.src && !source.src) {
                        source.src = source.dataset.src;
                    }
                });
                el.load();
                el.play().catch(() => {});
            }
        });
    </script>
@endsection
