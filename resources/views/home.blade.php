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
    @include('components.svg.up')
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
                .popup-inner img,
                .popup-inner video {
                    max-width: 100%;
                    height: auto;
                }

                .popup-inner img.tall-media,
                .popup-inner video.tall-media {
                    transform: scale(0.75);
                    transform-origin: center top;
                    margin-top: 60px;
                }
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
        font-size: 20px;
        padding: 4px 10px;
        border-radius: 4px;
        transition: none;
        background: transparent;
        color: #fff;
    }

    .project-link-text:hover {
        background: transparent !important;
        color: #fff !important; /* или inherit, если нужно */
    }

/* Повышаем специфичность и применяем !important */
#scroll-to-top {
    position: fixed !important;
    bottom: 30px !important;
    right: 30px !important;
    cursor: pointer !important;
    z-index: 9999 !important;
    display: none !important;
    transition: opacity 0.3s ease !important;
    background-color: #ccc !important;
    color: #000 !important; /* теперь цвет влияет на SVG */
    border-radius: 50% !important;
    width: 60px !important;
    height: 60px !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    box-shadow: 0 0 8px #0005 !important;
}

#scroll-to-top svg {
    width: 30px !important;
    height: 30px !important;
    transition: fill 0.2s ease !important;
    display: block;
    /* fill теперь управляется через currentColor → color родителя */
}

#scroll-to-top:hover {
    color: white !important; /* при hover → цвет SVG меняется */
}
.popup-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    z-index: 99999;
}

.popup-arrow,
.popup-close {
    font-size: 32px;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    user-select: none;
    transition: background 0.2s;
}

/* 📱 Мобильная версия — стрелки и крестик снизу */
@media (max-width: 768px) {
    .popup-controls {
        position: fixed;
        bottom: 20px;
        left: 0;
        width: 100%;
        justify-content: center;
        gap: 20px;
    }
}

/* 🖥️ Десктоп — стрелки по бокам, крестик в углу */
@media (min-width: 769px) {
    .popup-controls {
        position: static;
    }
    .popup-arrow.prev {
        position: fixed;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10000;
    }

    .popup-arrow.next {
        position: fixed;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10000;
    }

    .popup-close {
        position: fixed;
        top: 20px;
        right: 30px;
        font-size: 40px;
        font-weight: bold;
        z-index: 10000;
        background: none;
        padding: 0;
    }

    .popup-close svg {
        width: 20px;
        height: 20px;
        fill: #fff;
    }

    @media (max-width: 576px) {
    .popup-inner.tall-adjust {
        transform: translateY(60px); /* 💡 Сдвигаем весь блок вниз */
    }

    .popup-inner img.tall-media,
    .popup-inner video.tall-media {
        transform: scale(0.75);
        transform-origin: center top;
    }
}
}
    </style>
    {{-- / Внутренние стили главной --}}

    {{-- JS для popup --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    function checkTitleOverlap(titleElement, buttonContainer) {
        const popupInner = titleElement.closest('.popup-inner');
        const titleRect = titleElement.getBoundingClientRect();
        const buttonRect = buttonContainer.getBoundingClientRect();

        const verticalOverlap = !(titleRect.bottom < buttonRect.top || titleRect.top > buttonRect.bottom);
        const horizontalOverlap = !(titleRect.right < buttonRect.left || titleRect.left > buttonRect.right);

        const isOverlapping = verticalOverlap && horizontalOverlap;

        // Заголовок отдельно поднимаем как раньше
        titleElement.style.top = isOverlapping ? '-43px' : '0';

        // Сдвигаем popup-inner при необходимости
        if (isOverlapping) {
            if (window.innerWidth <= 768) {
                popupInner.style.marginTop = '60px';
            } else {
                popupInner.style.marginTop = '40px';
            }
        } else {
            popupInner.style.marginTop = '0';
        }
    }

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
                img.onload = function () {
                    if (this.naturalHeight / this.naturalWidth > 1.5) {
                        this.classList.add('tall-media');
                        popupInner.classList.add('tall-adjust');
                    }
                };
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

                // Проверка пропорций после метаданных
                video.onloadedmetadata = function () {
                    if (video.videoHeight / video.videoWidth > 1.5) {
                        video.classList.add('tall-media');
                        popupInner.classList.add('tall-adjust');
                    }
                };

                popupInner.appendChild(video);
            }

            // 🔧 Верхняя строка: заголовок + кнопки
            const topBar = document.createElement('div');

            if (window.innerWidth <= 576) {
                // 📱 Мобильная версия — нормальное поведение
                topBar.style.display = 'flex';
                topBar.style.flexDirection = 'row';
                topBar.style.justifyContent = 'space-between';
                topBar.style.alignItems = 'center';
                topBar.style.gap = '10px';
                topBar.style.width = '100%';
                topBar.style.padding = '12px';
                topBar.style.boxSizing = 'border-box';
            } else {
                // 🖥️ Десктоп — оставляем абсолют
                topBar.style.position = 'absolute';
                topBar.style.top = '-35px';
                topBar.style.left = '10px';
                topBar.style.right = '10px';
                topBar.style.zIndex = '2';
                topBar.style.height = '30px';
            }

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
                link.style.top = '-10px';
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

                    // Очистка text2 от пустых блоков и лишнего HTML
                    const cleanedText2 = (text2 || '')
                        .replace(/<p>(\s|&nbsp;|\u00A0)*<\/p>/gi, '') // удаляет пустые <p>, <p>&nbsp;</p>, <p> </p>
                        .replace(/<br\s*\/?>/gi, '')                  // удаляет <br>
                        .replace(/<ul>\s*<\/ul>/gi, '')               // удаляет пустые списки
                        .replace(/\s+$/, '');                         // удаляет пробелы в конце строки

                    infoBlock.innerHTML = cleanedText2;
                    popupInner.appendChild(infoBlock);
                }
            });

            buttonContainer.appendChild(infoIcon);

            topBar.appendChild(titleElement);
            topBar.appendChild(buttonContainer);
            popupInner.appendChild(topBar);


        // 🔁 ДОБАВЛЯЕМ кнопки переключения и крестик (внизу)
        const controls = document.createElement('div');
        controls.classList.add('popup-controls');

        // Вставка SVG из Blade — как строки
        const svgArrowPrev = `
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="17" viewBox="0 0 5560.1 4209.61">
        <path fill="#fff" d="M2340.98 210.48c0,141.49 -70.98,176.24 -157.86,263.11l-1293.84 1293.84c-35.58,35.58 -82.15,62.87 -95.94,114.53l4358.18 0c118.54,0 408.58,-41.91 408.58,222.86 0,251.74 -278.74,222.86 -396.2,222.86l-4382.96 0c38.3,57.18 1250.22,1256.4 1395.98,1402.17 88.34,88.33 164.05,126.02 164.05,269.29 0,100.49 -90.44,210.48 -185.71,210.48 -186.19,0 -199.36,-56.99 -368.33,-225.95l-1560.03 -1560.05c-100.61,-100.6 -354.78,-305.2 -148.24,-488.72 284.63,-252.88 654.83,-643.11 928.25,-916.54l922.39 -922.42c69.72,-70.21 82.9,-95.94 201.2,-95.94 109.53,0 210.48,100.96 210.48,210.48z"/>
        </svg>`;

        const svgArrowNext = `
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="17" viewBox="0 0 1091.71 826.54">
        <path fill="#fff" d="M632.07 41.33c0,27.78 13.94,34.6 31,51.66l254.04 254.04c6.99,6.99 16.13,12.34 18.84,22.49l-855.72 0c-23.27,0 -80.22,-8.23 -80.22,43.76 0,49.43 54.73,43.76 77.79,43.76l860.58 0c-7.52,11.23 -245.48,246.69 -274.1,275.31 -17.35,17.34 -32.21,24.74 -32.21,52.87 0,19.73 17.76,41.33 36.46,41.33 36.56,0 39.14,-11.19 72.32,-44.36l306.31 -306.31c19.76,-19.75 69.66,-59.93 29.11,-95.96 -55.89,-49.65 -128.57,-126.27 -182.26,-179.96l-181.11 -181.11c-13.69,-13.78 -16.28,-18.84 -39.51,-18.84 -21.51,0 -41.33,19.82 -41.33,41.33z"/>
        </svg>`;

        const svgClose = `
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 960.11 960.1">
        <path fill="#fff" d="M8.07 911.53l431.48 -431.48 -431.48 -431.48c-10.76,-10.76 -10.76,-28.37 0,-39.13l1.37 -1.37c10.76,-10.76 28.37,-10.76 39.13,0l431.48 431.48 431.48 -431.48c10.76,-10.76 28.37,-10.76 39.13,0l1.37 1.37c10.76,10.76 10.76,28.37 0,39.13l-431.48 431.48 431.48 431.48c10.76,10.76 10.76,28.37 0,39.13l-1.37 1.37c-10.76,10.76 -28.37,10.76 -39.13,0l-431.48 -431.48 -431.48 431.48c-10.76,10.76 -28.37,10.76 -39.13,0l-1.37 -1.37c-10.76,-10.76 -10.76,-28.37 0,-39.13z"/>
        </svg>`;

        const btnPrev = document.createElement('div');
        btnPrev.classList.add('popup-arrow', 'prev');
        btnPrev.innerHTML = svgArrowPrev;
        btnPrev.onclick = function () {
            const newIndex = (i - 1 + gridItemLinks.length) % gridItemLinks.length;
            gridItemLinks[newIndex].click();
        };

        const btnNext = document.createElement('div');
        btnNext.classList.add('popup-arrow', 'next');
        btnNext.innerHTML = svgArrowNext;
        btnNext.onclick = function () {
            const newIndex = (i + 1) % gridItemLinks.length;
            gridItemLinks[newIndex].click();
        };

        const btnClose = document.createElement('div');
        btnClose.classList.add('popup-close');
        btnClose.innerHTML = svgClose;
        btnClose.onclick = function () {
            popup.innerHTML = '';
            popup.classList.remove('active');
        };

        controls.append(btnPrev, btnClose, btnNext);
        popupInner.appendChild(controls);

            // 💡 Проверка наложения заголовка на кнопки (только для мобильной версии)
            setTimeout(() => {
                checkTitleOverlap(titleElement, buttonContainer);
            }, 300);

            window.addEventListener('resize', () => {
                if (popup.classList.contains('active')) {
                    checkTitleOverlap(
                        popup.querySelector('h3'),
                        popup.querySelector('.info-button')?.parentElement // там где View Full Project + info
                    );
                }
            });

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
