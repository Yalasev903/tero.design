        @php
            $showreel = \DB::table('home_projects_grid')->where('row_number', 0)->first();
            $media = json_decode($showreel?->media ?? '', true);
            $poster = $media['poster'] ?? $media['link'] ?? '';
            $posterSrc = $poster ? '/multimedia/' . $poster : '';
            $posterSrc .= $poster && file_exists(public_path('multimedia/' . $poster)) ? '?v=' . filemtime(public_path('multimedia/' . $poster)) : '';
            $videoMp4 = $media['links'][0]['link'] ?? '';
            $videoWebm = preg_replace('/\.(mp4|mov|mkv)$/i', '.webm', $videoMp4);
        @endphp

        <div class="showreel" id="js-showreel">
            <div class="showreel-center">
                <div class="showreel-poster-block" id="showreel-poster-block">
                    <img id="showreel-poster-img" alt="Showreel Poster">
                    <button type="button" class="showreel-play js-video-play">
                        <svg width="140" height="140" viewBox="0 0 140 140" fill="none">
                            <circle cx="70" cy="70" r="65" stroke="#fff" stroke-width="6"/>
                            <polygon points="58,45 105,70 58,95" fill="#fff"/>
                        </svg>
                    </button>
                    <div class="showreel-title">SHOWREEL</div>
                </div>
                <video id="js-video"
                    class="showreel-player-video"
                    preload="metadata"
                    controls
                    playsinline
                    muted
                    style="display:none;">
                    <!-- Sources вставим позже через JS -->
                </video>
            </div>
            <a href="#" class="showreel-close" id="js-showreel-close">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" fill="#fff" width="48" height="48">
                    <path d="M574.9,500L977.7,97.3c18.1-18.1,16-49.6-4.7-70.2c-20.7-20.7-52.1-22.8-70.2-4.7L500,425.1L97.3,22.3C79.1,4.2,47.7,6.3,27,27C6.3,47.7,4.2,79.1,22.3,97.3L425.1,500L22.3,902.7C4.2,920.9,6.3,952.3,27,973c20.7,20.7,52.1,22.8,70.2,4.7L500,574.9l402.7,402.7c18.1,18.1,49.6,16,70.2-4.7c20.7-20.7,22.8-52.1,4.7-70.2L574.9,500L574.9,500z"/>
                </svg>
            </a>
        </div>
