@php
    // прямой линк, жёстко как ты просил
@endphp

<div class="showreel" id="js-showreel">
    <div class="showreel-center">
        {{-- Вставка Vimeo --}}
        <div id="js-vimeo" class="showreel-player-video">
            <div style="padding:56.25% 0 0 0;position:relative;">
                <iframe id="vimeo-player"
                        src="https://player.vimeo.com/video/1015356904?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479"
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;"
                        title="Terodesign Showreel 2024">
                </iframe>
            </div>
        </div>

        {{-- Кнопка закрытия --}}
        <a href="#" class="showreel-close" id="js-showreel-close">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" fill="#fff" width="48" height="48">
                <path d="M574.9,500L977.7,97.3c18.1-18.1,16-49.6-4.7-70.2c-20.7-20.7-52.1-22.8-70.2-4.7L500,425.1L97.3,22.3C79.1,4.2,47.7,6.3,27,27C6.3,47.7,4.2,79.1,22.3,97.3L425.1,500L22.3,902.7C4.2,920.9,6.3,952.3,27,973c20.7,20.7,52.1,22.8,70.2,4.7L500,574.9l402.7,402.7c18.1,18.1,49.6,16,70.2-4.7c20.7-20.7,22.8-52.1,4.7-70.2L574.9,500L574.9,500z"/>
            </svg>
        </a>
    </div>
</div>
<script src="https://player.vimeo.com/api/player.js"></script>

