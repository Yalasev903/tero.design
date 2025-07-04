@php
    $isMobile = preg_match('/Mobile|Android|iPhone|iPad/i', request()->header('User-Agent'));
@endphp

@foreach($projects_grid as $row)
    @php
        $normalizedRow = [];
        $aspectTotal = 0;
        $inverseAspectMax = 0;

        foreach ($row as $col) {
            $media = is_array($col['media']) ? $col['media'] : json_decode($col['media'] ?? '', true);
            $width = (float)($media['width'] ?? 16);
            $height = (float)($media['height'] ?? 9);
            $aspect = $width > 0 && $height > 0 ? $width / $height : 1;
            $inverse = $aspect > 0 ? 1 / $aspect : 1;

            $normalizedRow[] = [
                'col' => $col,
                'media' => $media,
                'aspect' => $aspect,
                'width' => $width,
                'height' => $height,
                'inverse' => $inverse,
            ];
            $aspectTotal += $aspect;
            if ($inverse > $inverseAspectMax) {
                $inverseAspectMax = $inverse; // наибольшая относительная высота
            }
        }

        $rowHeightVw = $inverseAspectMax * 100; // итоговая высота строки в vw
    @endphp

    <div class="grid-row" style="height: {{ number_format($rowHeightVw, 2, '.', '') }}vw;">
        @foreach($normalizedRow as $entry)
            @php
                $col = $entry['col'];
                $media = $entry['media'];
                $aspect = $entry['aspect'];
                $width = $entry['width'];
                $height = $entry['height'];
                $inverse = $entry['inverse'];
                $showOnMobile = $col['is_mobile'] ?? false;
                $mediaLink = $media['link'] ?? '';
                $poster = $media['poster'] ?? $mediaLink;
                $normalizedGrow = $aspectTotal > 0 ? ($aspect / $aspectTotal) * 100 : 100;
            @endphp

            @if(!$isMobile || ($isMobile && $showOnMobile))
                <div class="grid-item-wrapper" style="flex: {{ number_format($normalizedGrow, 4, '.', '') }} 0 0%;">
                    <a href="#"
                       data-image="/multimedia/{{ $mediaLink }}"
                       data-video="@if(($media['type'] ?? '') === 'video')/multimedia/{{ $media['links'][0]['link'] ?? '' }}@endif"
                       class="grid-item {{ $showOnMobile ? 'grid-item-mobile' : 'grid-item-desktop' }} grid-item-{{ $media['type'] ?? '' }}"
                       data-media-width="{{ $width }}"
                       data-media-height="{{ $height }}"
                       data-project-link="{{ route('projects.show', ['id' => $col['project_id']]) }}"
                       data-text2="{{ $col['text2'] ?? '' }}">

                        @if(($media['type'] ?? '') === 'img')
                            <img
                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                data-src="/multimedia/{{ $mediaLink }}"
                                alt="{{ $media['alt'] ?? '' }}"
                                class="js-grid-item-media lazyload"
                            />
                        @elseif(($media['type'] ?? '') === 'video')
                            <video
                                muted
                                loop
                                playsinline
                                preload="auto"
                                class="js-grid-item-media lazyload"
                                data-autoplay
                                data-poster="/multimedia/{{ $poster }}"
                            >
                                @foreach($media['links'] ?? [] as $link)
                                    <source src="/multimedia/{{ $link['link'] ?? '' }}" type="{{ $link['mime'] ?? 'video/mp4' }}">
                                @endforeach
                            </video>
                        @endif

                        <h3 class="grid-item-title">{{ $col['title'] ?? '' }}</h3>
                    </a>
                </div>
            @endif
        @endforeach
    </div>
@endforeach

<style>
.grid-row {
  display: flex;
  flex-wrap: nowrap;
  width: 100%;
  align-items: stretch;
}

.grid-item-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: stretch;
}

.grid-item {
  width: 100%;
  height: auto;
  display: block;
  position: relative;
}

.grid-item img,
.grid-item video {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
  background-color: #000;
}
</style>

<script>
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    const video = entry.target;
    if (entry.isIntersecting) {
      if (video.readyState >= 2) {
        video.play().catch(() => {});
      }
    } else {
      video.pause();
    }
  });
}, {
  threshold: 0.5
});

document.querySelectorAll('video.js-grid-item-media').forEach(video => {
  observer.observe(video);
});
</script>
