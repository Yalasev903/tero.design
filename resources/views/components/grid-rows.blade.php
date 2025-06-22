@php
    $isMobile = preg_match('/Mobile|Android|iPhone|iPad/i', request()->header('User-Agent'));
@endphp

@foreach($projects_grid as $row)
    <div class="grid-row">
        @foreach($row as $col)
            @php
                $showOnMobile = $col['is_mobile'] ?? false;
                $media = is_array($col['media']) ? $col['media'] : json_decode($col['media'] ?? '', true);
                $mediaLink = $media['link'] ?? '';
                $poster = $media['poster'] ?? $mediaLink;
            @endphp

            {{-- Показываем всё на десктопе, на мобилке только is_mobile === true --}}
            @if(!$isMobile || ($isMobile && $showOnMobile))
                <a href="#"
                   data-image="/multimedia/{{ $mediaLink }}"
                   data-video="@if(($media['type'] ?? '') === 'video')/multimedia/{{ $media['links'][0]['link'] ?? '' }}@endif"
                   class="grid-item loading {{ $showOnMobile ? 'grid-item-mobile' : 'grid-item-desktop' }} grid-item-{{ $media['type'] ?? '' }}"
                   data-media-width="{{ $media['width'] ?? '' }}"
                   data-media-height="{{ $media['height'] ?? '' }}"
                   data-project-link="{{ route('projects.show', ['id' => $col['project_id']]) }}"
                   data-text2="{{ $col['text2'] ?? '' }}">

                    @if(($media['type'] ?? '') === 'img')
                        <img
                            src="/img/placeholder.png"
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
            @endif
        @endforeach
    </div>
@endforeach
