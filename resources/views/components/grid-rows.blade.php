@foreach($projects_grid as $row)
    <div class="grid-row">
        @foreach($row as $col)
            @php $media = json_decode($col['media'], true); @endphp
            <a href="#"
               data-image="/multimedia/{{ $media['link'] ?? '' }}"
               data-video="@if(($media['type'] ?? '') == 'video')/multimedia/{{ $media['links'][0]['link'] ?? '' }}@endif"
               class="grid-item loading {{ ($col['is_mobile'] ?? false) ? 'grid-item-mobile' : 'grid-item-desktop' }} grid-item-{{ $media['type'] ?? '' }}"
               data-media-width="{{ $media['width'] ?? '' }}"
               data-media-height="{{ $media['height'] ?? '' }}"
               data-project-link="{{ route('projects.show', ['id' => $col['project_id']]) }}"
               data-text2="{{ $col['text2'] ?? '' }}">
                @if(($media['type'] ?? '') === 'img')
                    <img
                        data-src="/multimedia/{{ $media['link'] ?? '' }}"
                        alt="{{ $media['alt'] ?? '' }}"
                        class="js-grid-item-media lazyload" />
                @elseif(($media['type'] ?? '') === 'video')
                    <video
                        muted
                        loop
                        playsinline
                        preload="none"
                        class="js-grid-item-media lazyload"
                        data-autoplay
                    >
                        @foreach($media['links'] ?? [] as $link)
                            <source data-src="/multimedia/{{ $link['link'] ?? '' }}" type="{{ $link['mime'] ?? '' }}">
                        @endforeach
                    </video>
                @endif
                <h3 class="grid-item-title">{{ $col['title'] ?? '' }}</h3>
            </a>
        @endforeach
    </div>
@endforeach
