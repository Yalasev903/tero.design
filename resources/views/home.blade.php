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
    transition: all 0.2s ease;
    }

    .project-link-text:hover {
        background: #000;
        color: #ccc;
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
    background: rgba(0,0,0,0.6);
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
        width: 32px;
        height: 32px;
        fill: #fff;
    }
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
                img.onload = function () {
                    if (this.naturalHeight / this.naturalWidth > 1.5) {
                        this.classList.add('tall-media');
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
                    }
                };

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

        const btnPrev = document.createElement('div');
        btnPrev.classList.add('popup-arrow', 'prev');
        btnPrev.innerHTML = `<svg viewBox="0 0 914 534" xmlns="http://www.w3.org/2000/svg"><path d="M914 267C914 288.025 896.931 305.126 875.944 305.126H129.932L293.443 468.938C308.273 483.796 308.273 507.998 293.443 522.856C278.612 537.714 254.454 537.714 239.623 522.856L11.0997 293.913C7.5553 290.362 4.75707 286.25 2.89157 281.578C1.02607 276.905 1.75086e-05 272.046 1.75086e-05 267.187C1.75086e-05 267.093 1.75086e-05 267.093 1.75086e-05 267C1.75086e-05 262.047 1.02605 257.095 2.98482 252.422C4.85032 247.75 7.74183 243.545 11.193 240.087L239.716 11.1435C254.547 -3.7145 278.705 -3.7145 293.536 11.1435C308.367 26.0015 308.367 50.2041 293.536 65.0621L130.025 228.874H875.944C896.931 228.874 914 245.975 914 267Z" fill="#fff"/></svg>`;
        btnPrev.onclick = function () {
            const newIndex = (i - 1 + gridItemLinks.length) % gridItemLinks.length;
            gridItemLinks[newIndex].click();
        };

        const btnNext = document.createElement('div');
        btnNext.classList.add('popup-arrow', 'next');
        btnNext.innerHTML = `<svg viewBox="0 0 914 534" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 267C0 288.025 17.0693 305.126 38.0561 305.126H784.068L620.557 468.938C605.727 483.796 605.727 507.998 620.557 522.856C635.388 537.714 659.546 537.714 674.377 522.856L902.9 293.913C906.445 290.362 909.243 286.25 911.108 281.578C912.974 276.905 914 272.046 914 267.187C914 267.093 914 267.093 914 267C914 262.047 912.974 257.095 911.015 252.422C909.15 247.75 906.258 243.545 902.807 240.087L674.284 11.1435C659.453 -3.7145 635.295 -3.7145 620.464 11.1435C605.633 26.0015 605.633 50.2041 620.464 65.0621L783.975 228.874H38.0561C17.0693 228.874 0 245.975 0 267Z" fill="#fff"/></svg>`;
        btnNext.onclick = function () {
            const newIndex = (i + 1) % gridItemLinks.length;
            gridItemLinks[newIndex].click();
        };

        const btnClose = document.createElement('div');
        btnClose.classList.add('popup-close');
        btnClose.innerHTML = `<svg viewBox="0 0 24 24" fill="#fff" xmlns="http://www.w3.org/2000/svg"><path d="M18.3 5.71a1 1 0 00-1.42 0L12 10.59 7.12 5.7A1 1 0 105.7 7.12L10.59 12l-4.9 4.88a1 1 0 001.42 1.42L12 13.41l4.88 4.9a1 1 0 001.42-1.42L13.41 12l4.9-4.88a1 1 0 000-1.41z"/></svg>`;
        btnClose.onclick = function () {
            popup.innerHTML = '';
            popup.classList.remove('active');
        };

        controls.append(btnPrev, btnClose, btnNext);
        popupInner.appendChild(controls);


            // 💡 Проверка наложения заголовка на кнопки (только для мобильной версии)
            setTimeout(() => {
                const isMobile = window.innerWidth <= 768;
                if (!isMobile) return;

                const titleRect = titleElement.getBoundingClientRect();
                const buttonRect = buttonContainer.getBoundingClientRect();

                const overlap = !(titleRect.bottom < buttonRect.top ||
                                titleRect.top > buttonRect.bottom ||
                                titleRect.right < buttonRect.left ||
                                titleRect.left > buttonRect.right);

                if (overlap) {
                    titleElement.style.top = '-43px';
                }
            }, 100);

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
