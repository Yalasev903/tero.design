<div class="showreel" id="js-showreel">
    <div class="showreel-center">
        {{-- Вставка Vimeo --}}
        <div id="js-vimeo" style="width: 100%; max-width: 1920px;">
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                @if ($vimeoLink)
                    <iframe id="vimeo-player"
                            src="{{ $vimeoLink }}"
                            style="position:absolute; top:0; left:0; width:100%; height:100%;"
                            frameborder="0"
                            allow="fullscreen; picture-in-picture"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                            title="Tero Design Showreel">
                    </iframe>
                @else
                    <div style="color: white; text-align: center; padding: 40px;">Видео временно недоступно</div>
                @endif
            </div>
        </div>

        {{-- Кнопка закрытия --}}
        <a href="#" class="showreel-close" id="js-showreel-close">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" fill="#fff" width="48" height="48">
                <path d="M574.9,500L977.7,97.3c18.1-18.1,16-49.6-4.7-70.2c-20.7-20.7-52.1-22.8-70.2-4.7L500,425.1L97.3,22.3C79.1,4.2,47.7,6.3,27,27C6.3,47.7,4.2,79.1,22.3,97.3L425.1,500L22.3,902.7C4.2,920.9,6.3,952.3,27,973c20.7,20.7,52.1,22.8,70.2,4.7L500,574.9l402.7,402.7c18.1,18.1,49.6,16,70.2-4.7c20.7-20.7,22.8-52.1,4.7-70.2L574.9,500z"/>
            </svg>
        </a>
    </div>
</div>

<script src="https://player.vimeo.com/api/player.js"></script>

