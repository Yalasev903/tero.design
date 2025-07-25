@extends('layouts.app')

@section('header_title', $project->meta_title ?? $project->title)
@section('header_description', $project->meta_description)
@section('header_keywords', $project->meta_keywords)

@section('content')
@php
  $mediaPath = fn($path) => str_starts_with($path, 'multimedia/') ? '/' . ltrim($path, '/') : '/multimedia/' . ltrim($path, '/');
@endphp

<div class="project">
  <div class="project-head row2">
    <h1 class="project-title">{{ $project->title }}</h1>
    <a href="{{ route('home') }}#js-grid-item{{ $project->id }}" class="project-back-icon">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="48" height="48" fill="#fff">
        <path d="M136.97 380.485l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L60.113 273H436c6.627 0 12-5.373 12-12v-10c0-6.627-5.373-12-12-12H60.113l83.928-83.444c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0l-116.485 116c-4.686 4.686-4.686 12.284 0 16.971l116.485 116c4.686 4.686 12.284 4.686 16.97-.001z"/>
      </svg>
    </a>
  </div>

  <div class="project-description">
    <div class="project-description-col">{!! $project->text1 !!}</div>
    <div class="project-description-col">{!! $project->text2 !!}</div>
  </div>
</div>

<div class="grid" id="js-gallery">
  @php $grid = $project->multimedia_grid; @endphp

@foreach ($grid as $rowIndex => $row)
@php
    $preparedCols = [];

    foreach ($row as $col) {
        $w = $col['width'] ?? null;
        $h = $col['height'] ?? null;

        // Попытка получить размеры, если они не заданы
        if ((!$w || !$h) && in_array($col['type'], ['img', 'video']) && !empty($col['link'])) {
            $path = public_path(ltrim($col['link'], '/'));
            if (file_exists($path)) {
                [$w, $h] = getimagesize($path);
            }
        }

        // Запасной вариант, если ничего не вышло
        if (!$w || !$h) {
            $w = 1920;
            $h = 1080;
        }

        $ratio = $w / $h;
        $preparedCols[] = ['col' => $col, 'w' => $w, 'h' => $h, 'ratio' => $ratio];
    }

    $rowWidth = 1920;

    $colsCount = count($preparedCols);
    $maxRowHeight = match (true) {
        $colsCount === 1 => 1080,
        $colsCount === 2 => 744,
        $colsCount === 3 => 520,
        $colsCount === 4 => 420,
        default => 360,
    };

    // 🔁 Подбор финальной высоты строки по фактическим пропорциям
    $finalHeight = null;
    $step = 10;

    for ($h = 200; $h <= $maxRowHeight; $h += $step) {
        $totalWidth = 0;

        foreach ($preparedCols as $entry) {
            $totalWidth += ($entry['w'] / $entry['h']) * $h;
        }

        if ($totalWidth >= $rowWidth) {
            $finalHeight = $h;
            break;
        }
    }

    // Если не набралась ширина — берём максимально допустимую
    $finalHeight = $finalHeight ?? $maxRowHeight;

    // Теперь можно снова посчитать ratio/percent
    $totalRatio = array_sum(array_map(function ($entry) {
        return $entry['w'] / $entry['h'];
    }, $preparedCols));
@endphp

  <div class="grid-row" style="height: {{ round($finalHeight) }}px; display: flex;">
    @foreach ($preparedCols as $entry)
      @php
        $col = $entry['col'];
        $w = $entry['w'];
        $h = $entry['h'];
        $ratio = $entry['ratio'];
        $flexPercent = round(($ratio / $totalRatio) * 100, 6);
        $link = ltrim($col['link'] ?? '', '/');
        $url = '/' . $link;
      @endphp

      @switch($col['type'])

        @case('img')
          <a href="{{ $url }}"
             class="grid-item js-img"
             style="flex: 0 0 calc({{ $flexPercent }}%);"
             data-media-width="{{ $w }}" data-media-height="{{ $h }}">
            <div class="grid-inner-wrapper">
              <img src="{{ $url }}"
                   alt="{{ $col['description'] ?? '' }}"
                   width="{{ $w }}" height="{{ $h }}"
                   class="js-grid-item-media lazyload" />
            </div>
          </a>
          @break

        @case('video')
        <div class="grid-item"
            style="flex: 0 0 calc({{ $flexPercent }}%);"
            data-media-width="{{ $w }}" data-media-height="{{ $h }}">
            <div class="video-wrapper"
                style="position: relative; width: 100%; padding-bottom: {{ round(100 * $h / $w, 4) }}%;">
            <video preload="metadata"
                    playsinline
                    muted
                    loop
                    autoplay
                    class="js-grid-item-media lazyload"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: block;">
                @foreach ($col['links'] ?? [] as $source)
                <source src="{{ '/' . ltrim($source['link'], '/') }}"
                        type="{{ $source['mime'] ?? 'video/mp4' }}">
                @endforeach
            </video>
            </div>
        </div>
        @break

        @case('vr')
          @php
            $iframeSrc = $col['link'];
            if (!str_starts_with($iframeSrc, '<iframe')) {
              $iframeSrc = '<iframe src="' . e($iframeSrc) . '" frameborder="0" allowfullscreen allow="xr-spatial-tracking; gyroscope; accelerometer" scrolling="no" style="width:100%;height:100%"></iframe>';
            }
          @endphp
          <div class="grid-item"
               style="flex: 0 0 calc({{ $flexPercent }}%);"
               data-media-width="{{ $w }}" data-media-height="{{ $h }}">
            <div class="grid-inner-wrapper vr-wrapper">
              {!! $iframeSrc !!}
            </div>
          </div>
          @break

        @case('curtain')
          @php
            $isNewFormat = isset($col['images']);
            $img1Path = $isNewFormat ? ($col['images'][0] ?? '') : ($col['first']['link'] ?? '');
            $img2Path = $isNewFormat ? ($col['images'][1] ?? '') : ($col['last']['link'] ?? '');

            $img1 = '/' . ltrim($img1Path, '/');
            $img2 = '/' . ltrim($img2Path, '/');

            $img1W = 1920; $img1H = 1080;
            if (!empty($img1Path)) {
                $full = public_path(ltrim($img1Path, '/'));
                if (file_exists($full)) {
                    [$img1W, $img1H] = getimagesize($full);
                }
            }
          @endphp

          <div class="grid-item curtain-container"
               style="flex: 0 0 calc({{ $flexPercent }}%);"
               data-img1="{{ $img1 }}"
               data-img2="{{ $img2 }}"
               data-media-width="{{ $img1W }}"
               data-media-height="{{ $img1H }}">
            <div class="grid-inner-wrapper">
              <canvas class="curtain-canvas"></canvas>
              <div class="curtain-handle">
                <span class="curtain-arrow left">←</span>
                <span class="curtain-arrow right">→</span>
              </div>
            </div>
          </div>
          @break

        @default
          <div class="grid-item" style="flex: 0 0 calc({{ $flexPercent }}%);">
            <div class="grid-inner-wrapper">
              {{ $col['link'] ?? 'error type' }}
            </div>
          </div>

      @endswitch
    @endforeach
  </div>
@endforeach
</div>
<script>
(function () {
    const loader = document.querySelector('.loader');
    const grid = document.getElementById('js-gallery');
    const loadingVideo = document.getElementById('loading-video-banner');

    const showItemsSequentially = (items, index = 0) => {
        if (index >= items.length) return;
        const item = items[index];
        item.classList.add('visible');

        setTimeout(() => {
            requestAnimationFrame(() => {
                showItemsSequentially(items, index + 1);
            });
        }, 80);
    };

    const revealGrid = () => {
        if (grid) {
            grid.style.opacity = '1';
            const items = grid.querySelectorAll('.grid-item');
            showItemsSequentially(items);
        }
    };

    const hideLoader = () => {
        loader.style.opacity = '0';
        loader.style.pointerEvents = 'none';
        setTimeout(() => loader.remove(), 600);
        document.body.classList.remove('loading');
    };

    const init = () => {
        revealGrid(); // 👈 Показываем сетку СРАЗУ

        if (loadingVideo && loadingVideo.readyState >= 3) {
            loadingVideo.addEventListener('ended', hideLoader);
            setTimeout(hideLoader, 3600); // запас на 1 сек
        } else {
            setTimeout(hideLoader, 1000);
        }
    };

    if (document.readyState === 'complete') {
        init();
    } else {
        window.addEventListener('load', init);
    }
})();
</script>
<script>
function initCurtainCanvas(canvas, img1src, img2src) {
  const ctx = canvas.getContext('2d');
  const handle = canvas.parentElement.querySelector('.curtain-handle');

  const img1 = new Image();
  const img2 = new Image();

  let divider = 0.5;
  let dragging = false;

  // Основной рендер
 const draw = () => {
  const w = canvas.offsetWidth;
  const aspect1 = img1.naturalWidth / img1.naturalHeight;
  const aspect2 = img2.naturalWidth / img2.naturalHeight;
  const aspect = Math.min(aspect1, aspect2);
  const h = canvas.parentElement.offsetHeight;

  canvas.width = w;
  canvas.height = h;

  ctx.clearRect(0, 0, w, h);

  // Рисуем img2 на весь фон
  ctx.drawImage(img2, 0, 0, w, h);

  // Обрезаем левую часть
  ctx.save();
  ctx.beginPath();
  ctx.rect(0, 0, w * divider, h);
  ctx.clip();
  ctx.drawImage(img1, 0, 0, w, h);
  ctx.restore();
};

  const updateHandle = () => {
    handle.style.left = `${divider * 100}%`;
  };

  const onMove = e => {
    if (!dragging) return;
    const bounds = canvas.getBoundingClientRect();
    const x = (e.touches ? e.touches[0].clientX : e.clientX) - bounds.left;
    divider = Math.max(0.01, Math.min(0.99, x / bounds.width));
    updateHandle();
    draw();
  };

  handle.addEventListener('mousedown', () => (dragging = true));
  handle.addEventListener('touchstart', () => (dragging = true), { passive: true });
  window.addEventListener('mouseup', () => (dragging = false));
  window.addEventListener('touchend', () => (dragging = false));
  window.addEventListener('mousemove', onMove);
  window.addEventListener('touchmove', onMove, { passive: true });

  let ready = 0;
  const finalize = () => {
    if (++ready < 2) return;

    // сохраняем изображения на canvas для ресайза
    canvas._img1 = img1;
    canvas._img2 = img2;

    requestAnimationFrame(() => {
      canvas.style.width = '100%';
      canvas.style.height = '100%';
      draw();
      updateHandle();
    });
  };

  img1.onload = finalize;
  img2.onload = finalize;

  img1.src = img1src;
  img2.src = img2src;
}

// Инициализация на всех .curtain-container
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.curtain-container').forEach(container => {
    const canvas = container.querySelector('.curtain-canvas');
    const img1 = container.getAttribute('data-img1');
    const img2 = container.getAttribute('data-img2');
    initCurtainCanvas(canvas, img1, img2);
  });

  if (window.lightGallery) {
    lightGallery(document.getElementById('js-gallery'), {
      selector: '.js-img',
      download: false,
      zoom: true,
      fullScreen: true,
      share: false
    });
  }
});

// Адаптация при изменении размера окна
window.addEventListener('resize', () => {
  document.querySelectorAll('.curtain-canvas').forEach(canvas => {
    const img1 = canvas._img1;
    const img2 = canvas._img2;
    if (!img1 || !img2) return;

    const ctx = canvas.getContext('2d');
    const w = canvas.offsetWidth;
    const h = canvas.parentElement.offsetHeight;

    canvas.width = w;
    canvas.height = h;

    ctx.clearRect(0, 0, w, h);
    ctx.drawImage(img2, 0, 0, w, h);
    ctx.drawImage(img1, 0, 0, w * 0.5, h, 0, 0, w * 0.5, h);
  });
});
</script>

<style>
.grid-row {
  display: flex;
  flex-wrap: nowrap;
  /* gap: 1px; */
  width: 100%;
  align-items: stretch;
  /* overflow: hidden; */
}
.grid-item {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: stretch;
  height: 100%;
  overflow: hidden;
}

.grid-inner-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  overflow: hidden;
}
.grid-inner-wrapper > img {
  width: auto;
  height: 100%;
  display: block;
}
.grid-inner-wrapper > iframe,
.grid-inner-wrapper > canvas {
  max-width: 100%;
  max-height: 100%;
  width: 100%;
  height: auto;
  object-fit: contain;
  display: block;
}
.video-wrapper {
  position: relative;
  width: 100%;
  height: 0; /* Это важно: высота задаётся через padding-bottom */
  overflow: hidden;
}

.video-wrapper video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100% !important;
  height: 100% !important;
  display: block;
}
.grid-row.is-compact {
  height: auto;
}

.project-head {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding-right: 60px;
}
.vr-wrapper iframe {
  width: 100% !important;
  height: 100% !important;
  display: block;
  border: none;
}

.img-popup {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.img-popup-img {
  max-width: 90vw;
  max-height: 90vh;
  box-shadow: 0 0 20px rgba(255,255,255,0.1);
}

.img-popup-close {
  position: fixed;
  top: 20px; right: 30px;
  font-size: 40px;
  color: #fff;
  cursor: pointer;
  z-index: 10000;
  font-weight: bold;
}

.img-popup-arrow {
  position: fixed;
  top: 50%;
  font-size: 60px;
  color: #fff;
  cursor: pointer;
  user-select: none;
  z-index: 10000;
  transform: translateY(-50%);
}
.img-popup-arrow.prev { left: 20px; }
.img-popup-arrow.next { right: 20px; }

.img-popup-arrow:hover,
.img-popup-close:hover {
  color: #ccc;
}

.project-head {
  position: relative;
  padding-top: 20px;
  padding-bottom: 20px;
}

.project-back-icon {
  position: absolute;
  right: 0;
  top: 0;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;   /* Больше */
  height: 60px;  /* Больше */
  color: #fff;
  text-decoration: none;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.project-back-icon:hover {
  opacity: 1;
}


/* Curtain */
.grid-item.curtain img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.grid-item.curtain-container {
  display: block;
  width: 100%;
  height: auto;
}

.before-after-wrapper {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.curtain-container {
  position: relative;
  width: 100%;
  height: 100%; /* ← важно! чтобы соответствовать соседним */
 /* убираем ограничение */
}
.curtain-canvas {
  display: block;
  width: 100%;
  height: 100%;
  max-width: none;
  max-height: none;
}

.curtain-wrapper {
  position: relative;
  width: 100%;
  background: #000;
  overflow: hidden;
}

.curtain-img {
  position: absolute;
  width: 100%;
  height: 100%;
  object-fit: cover;
  top: 0;
  left: 0;
}
.curtain-before {
  z-index: 2;
  clip-path: inset(0 50% 0 0); /* ← начальное разделение */
  transition: clip-path 0.1s ease;
  background: transparent;
}

.curtain-after {
  z-index: 1;
  background: #000;
}

.curtain-handle {
  position: absolute;
  top: 0;
  left: 50%;
  width: 2px;
  height: 100%;
  background: rgba(255,255,255,0.7);
  z-index: 10;
  cursor: ew-resize;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: none;
}
.curtain-arrow {
  position: relative;
  font-size: 24px;
  font-weight: bold;
  color: white;
  text-shadow: 0 0 3px rgba(0, 0, 0, 0.8);
  pointer-events: none;
  user-select: none;
  padding: 0 6px;
}

.curtain-arrow.left {
  left: -33px;
}

.curtain-arrow.right {
  right: 33px;
}

.curtain-before,
.curtain-after {
  backface-visibility: hidden;
  will-change: clip-path;
  transition: none;
}

.curtain-img {
  opacity: 0;
  transition: opacity 0.3s ease;
}

.curtain-wrapper.loaded .curtain-img {
  opacity: 1;
}
.curtain-wrapper:not(.loaded) {
  visibility: hidden;
}

/* Mobile */
@media (max-width: 768px) {
  .grid-row {
    flex-direction: column;
    height: auto;
  }

  .grid-item {
    width: 100% !important;
    height: auto;
  }

  .grid-inner-wrapper {
    aspect-ratio: auto !important;
  }

  .grid-inner-wrapper > img,
  .grid-inner-wrapper > iframe,
  .grid-inner-wrapper > video {
    width: 100%;
    height: auto;
}
}
</style>
@endsection
