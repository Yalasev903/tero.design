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
   <img src="data:image/svg+xml;base64,PHN2ZyBmaWxsPSIjMDAwMDAwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTIgOC4zN2w2LjI5IDYuMjktMS40MSAxLjQxTDExLjk5IDEwLjgzbC02Ljg3IDYuODcgLTEuNDItMS40MUwxMiA4LjM3eiIvPjwvc3ZnPg==" alt="↑">
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
    background: #ccc;               /* светло-серый фон */
    color: #000;                    /* чёрный текст */
    text-decoration: none;
    font-size: 14px;
    padding: 4px 10px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.project-link-text:hover {
    background: #000;               /* чёрный фон при наведении */
    color: #ccc;                    /* светло-серый текст */
}
    #scroll-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    cursor: pointer;
    z-index: 9999;
    display: none;
    transition: opacity 0.3s ease;
}

#scroll-to-top img {
    width: 40px;
    height: 40px;
    filter: drop-shadow(0 0 3px #000a);
    transition: filter 0.2s ease, brightness 0.2s ease;
}

/* При наведении делаем белым */
#scroll-to-top:hover img {
    filter: brightness(10) drop-shadow(0 0 3px #000a);
}

    </style>
    {{-- / Внутренние стили главной --}}

    {{-- JS для popup --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var gridItemLinks = document.getElementsByClassName('grid-item');
    var popup = document.createElement('div');
    popup.classList.add('popup');
    document.body.appendChild(popup);

    for (var i = 0; i < gridItemLinks.length; i++) {
        gridItemLinks[i].addEventListener('click', function (e) {
            e.preventDefault();
            var mediaType = this.classList.contains('grid-item-video') ? 'video' : 'image';
            var mediaUrl = this.dataset.image;
            var title = this.querySelector('.grid-item-title').innerText;
            var projectLink = this.dataset.projectLink;
            var text2 = this.dataset.text2;

            var popupInner = document.createElement('div');
            popupInner.classList.add('popup-inner');

            // 🎥 Медиаконтент
            if (mediaType === 'image') {
                var img = document.createElement('img');
                img.src = mediaUrl;
                popupInner.appendChild(img);
            } else {
                mediaUrl = this.dataset.video;
                var video = document.createElement('video');
                video.preload = 'metadata';
                video.muted = true;
                video.loop = true;
                video.autoplay = true;
                var source = document.createElement('source');
                source.src = mediaUrl;
                video.appendChild(source);
                popupInner.appendChild(video);
            }

            // 🔧 Info-блок
            var projectInfo = document.createElement('div');
            projectInfo.classList.add('project-info');

            // Заголовок
            var titleElement = document.createElement('h3');
            titleElement.innerText = title;
            titleElement.classList.add('project-title');
            projectInfo.appendChild(titleElement);

            // Ссылка "View Full Project"
            if (projectLink) {
                var link = document.createElement('a');
                link.href = projectLink;
                link.target = '_blank';
                link.rel = 'noopener';
                link.classList.add('project-link-text');
                link.textContent = 'View Full Project';
                projectInfo.appendChild(link);
            }

            // Кнопка i
            var infoIcon = document.createElement('span');
            infoIcon.innerHTML = '<img class="i_svg" src="/multimedia/info.svg" alt="i">';
            infoIcon.classList.add('info-button');
            infoIcon.addEventListener('click', function () {
                var infoBlock = projectInfo.querySelector('.info-block');
                if (infoBlock) {
                    projectInfo.removeChild(infoBlock);
                } else {
                    infoBlock = document.createElement('div');
                    infoBlock.classList.add('info-block');
                    infoBlock.innerHTML = text2;
                    projectInfo.appendChild(infoBlock);
                }
            });
            projectInfo.appendChild(infoIcon);

            popupInner.appendChild(projectInfo);
            popup.innerHTML = '';
            popup.appendChild(popupInner);
            popup.classList.add('active');

            var mediaEl = popupInner.querySelector('img, video');
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

// lazyload
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

// Infinite scroll
let batch = 1;
let loading = false;

window.addEventListener('scroll', async () => {
    if (loading) return;

    const scrollPosition = window.innerHeight + window.scrollY;
    const pageHeight = document.body.offsetHeight;

    if (scrollPosition >= pageHeight - 500) {
        loading = true;
        try {
            const response = await fetch(`/?batch=${batch}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (result.rows.trim()) {
                document.querySelector('#js-gallery').insertAdjacentHTML('beforeend', result.rows);
                batch++;
                loading = false;
            }
        } catch (err) {
            console.error('Ошибка подгрузки:', err);
        }
    }
});
</script>

    {{-- / JS для popup --}}
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
