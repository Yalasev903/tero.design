@foreach($projects_grid as $row)
    <div class="grid-row">
        @foreach($row as $col)
            @php
                $media = is_array($col['media']) ? $col['media'] : json_decode($col['media'] ?? '', true);
                $mediaLink = $media['link'] ?? '';
                $poster = $media['poster'] ?? $mediaLink;
            @endphp
            <a href="#"
               data-image="/multimedia/{{ $mediaLink }}"
               data-video="@if(($media['type'] ?? '') === 'video')/multimedia/{{ $media['links'][0]['link'] ?? '' }}@endif"
               class="grid-item loading {{ ($col['is_mobile'] ?? false) ? 'grid-item-mobile' : 'grid-item-desktop' }} grid-item-{{ $media['type'] ?? '' }}"
               data-media-width="{{ $media['width'] ?? '' }}"
               data-media-height="{{ $media['height'] ?? '' }}"
               data-project-link="{{ route('projects.show', ['id' => $col['project_id']]) }}"
               data-text2="{{ $col['text2'] ?? '' }}">

                @if(($media['type'] ?? '') === 'img')
                    <img
                        src="/img/placeholder.png" {{-- предотвращает пустой фон до загрузки --}}
                        data-src="/multimedia/{{ $mediaLink }}"
                        alt="{{ $media['alt'] ?? '' }}"
                        class="js-grid-item-media lazyload"
                        style="background-color: #111; display: block;" />

                @elseif(($media['type'] ?? '') === 'video')
                    <video
                        muted
                        loop
                        playsinline
                        preload="none"
                        class="js-grid-item-media lazyload"
                        data-autoplay
                        data-poster="/multimedia/{{ $poster }}"
                        style="background-color: #000; display: block;"
                    >
                        @foreach($media['links'] ?? [] as $link)
                            <source data-src="/multimedia/{{ $link['link'] ?? '' }}" type="{{ $link['mime'] ?? 'video/mp4' }}">
                        @endforeach
                    </video>
                @endif

                <h3 class="grid-item-title">{{ $col['title'] ?? '' }}</h3>
            </a>
        @endforeach
    </div>
@endforeach
