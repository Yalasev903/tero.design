@php
    $isMobile = preg_match('/Mobile|Android|iPhone|iPad/i', request()->header('User-Agent'));
@endphp

@foreach($projects_grid as $row)
    @php
        $normalizedRow = [];
        $aspectTotal = 0;

        foreach ($row as $col) {
            $media = is_array($col['media']) ? $col['media'] : json_decode($col['media'] ?? '', true);
            $width = (float)($media['width'] ?? 1600);
            $height = (float)($media['height'] ?? 900);
            $aspect = $width > 0 && $height > 0 ? $width / $height : 1;

            $normalizedRow[] = [
                'col' => $col,
                'media' => $media,
                'aspect' => $aspect,
                'width' => $width,
                'height' => $height,
            ];

            $aspectTotal += $aspect;
        }

        $rowWidth = 1000;
    @endphp

    <div class="grid-row">
        @foreach($normalizedRow as $entry)
            @php
                $col = $entry['col'];
                $media = $entry['media'];
                $aspect = $entry['aspect'];
                $width = $entry['width'];
                $height = $entry['height'];
                $showOnMobile = $col['is_mobile'] ?? false;
                $mediaLink = $media['link'] ?? '';
                $poster = $media['poster'] ?? $mediaLink;
                $itemWidth = $aspectTotal > 0 ? ($aspect / $aspectTotal) * $rowWidth : 400;
                $hasProject = !empty($col['project_id']);
            @endphp

            @if(!$isMobile || ($isMobile && $showOnMobile))
                <a
                    @if($hasProject && ($col['has_link'] ?? true))
                        href="{{ route('projects.show', ['id' => $col['project_id']]) }}"
                        data-project-link="{{ route('projects.show', ['id' => $col['project_id']]) }}"
                    @else
                        href="javascript:void(0)"
                    @endif
                    data-image="/multimedia/{{ $mediaLink }}"
                    data-video="@if(($media['type'] ?? '') === 'video')/multimedia/{{ $media['links'][0]['link'] ?? '' }}@endif"
                    class="grid-item grid-item-{{ $media['type'] ?? '' }} {{ $showOnMobile ? 'grid-item-mobile' : 'grid-item-desktop' }} visible"
                    data-media-width="{{ $width }}"
                    data-media-height="{{ $height }}"
                    data-text2="{{ $col['text2'] ?? '' }}"
                    style="width: {{ number_format($itemWidth, 3, '.', '') }}px;"
                >
                    @if(($media['type'] ?? '') === 'img')
                        <img
                            src="/multimedia/{{ $mediaLink }}"
                            data-src="/multimedia/{{ $mediaLink }}"
                            alt="{{ $media['alt'] ?? '' }}"
                            class="js-grid-item-media lazyload" />
                    @elseif(($media['type'] ?? '') === 'video')
                        <video
                            muted
                            loop
                            playsinline
                            preload="auto"
                            class="js-grid-item-media lazyload"
                            data-autoplay
                            data-poster="/multimedia/{{ $poster }}">
                            @foreach($media['links'] ?? [] as $link)
                                <source src="/multimedia/{{ $link['link'] ?? '' }}" type="{{ $link['mime'] ?? 'video/mp4' }}">
                            @endforeach
                        </video>
                    @endif

                    <h3 class="grid-item-title">{{ $col['title'] ?? '' }}</h3>
                </a>
            @endif
        @endforeach
    </div>
@endforeach

<style>
.grid-row {
  display: flex;
  flex-wrap: nowrap;
  margin-bottom: 0;
  width: 100%;
  align-items: stretch;
  overflow: hidden;
}

.grid-item {
  display: block;
  flex-shrink: 0;
  position: relative;
}

.grid-item img,
.grid-item video {
  width: 100%;
  height: auto;
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
