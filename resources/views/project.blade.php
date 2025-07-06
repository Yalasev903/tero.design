@extends('layouts.app')

@section('title', $project->meta_title ?? $project->title)
@section('description', $project->meta_description)
@section('keywords', $project->meta_keywords)

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
  $totalRatio = 0;
  $preparedCols = [];

  foreach ($row as $col) {
      $w = $col['width'] ?? null;
      $h = $col['height'] ?? null;

      // Если размеров нет — пробуем получить их через getimagesize
      if (!$w || !$h) {
          $localPath = public_path('multimedia/' . ltrim($col['link'] ?? '', '/'));
          if (file_exists($localPath)) {
              [$imgW, $imgH] = getimagesize($localPath);
              $w = $imgW;
              $h = $imgH;
          } else {
              $w = 1920;
              $h = 1080;
          }
      }

      $ratio = $w / $h;
      $preparedCols[] = ['col' => $col, 'w' => $w, 'h' => $h, 'ratio' => $ratio];
      $totalRatio += $ratio;
  }
@endphp
<div class="grid-row" data-row-index="{{ $rowIndex }}">
  @foreach ($preparedCols as $entry)
    @php
      $col = $entry['col'];
      $w = $entry['w'];
      $h = $entry['h'];
      $ratio = $entry['ratio'];
      $widthPercent = ($ratio / $totalRatio) * 100;
    @endphp

    @switch($col['type'])

      @case('img')
        <a href="{{ $mediaPath($col['link']) }}"
           class="grid-item grid-item-img js-img"
           style="width: {{ $widthPercent }}%;"
           data-media-width="{{ $w }}" data-media-height="{{ $h }}">
          <div class="grid-inner-wrapper">
            <img src="{{ $mediaPath($col['link']) }}"
                 alt="{{ $col['description'] ?? '' }}"
                 width="{{ $w }}" height="{{ $h }}"
                 class="js-grid-item-media lazyload" />
          </div>
        </a>
        @break

      @case('video')
        <div class="grid-item"
             style="width: {{ $widthPercent }}%;"
             data-media-width="{{ $w }}" data-media-height="{{ $h }}">
          <div class="grid-inner-wrapper">
            <video preload="metadata" playsinline muted loop autoplay
                   width="{{ $w }}" height="{{ $h }}"
                   class="js-grid-item-media lazyload">
              @foreach ($col['links'] ?? [] as $source)
                <source src="{{ $mediaPath($source['link']) }}" type="{{ $source['mime'] ?? 'video/mp4' }}">
              @endforeach
            </video>
          </div>
        </div>
        @break

      @case('curtain')
        @php
          $isNewFormat = isset($col['images']);
          $img1 = $mediaPath($isNewFormat ? ($col['images'][0] ?? '') : ($col['first']['link'] ?? ''));
          $img2 = $mediaPath($isNewFormat ? ($col['images'][1] ?? '') : ($col['last']['link'] ?? ''));
        @endphp
        <div class="grid-item curtain-container"
             style="width: {{ $widthPercent }}%;"
             data-img1="{{ asset(ltrim($img1, '/')) }}"
             data-img2="{{ asset(ltrim($img2, '/')) }}"
             data-media-width="{{ $w }}"
             data-media-height="{{ $h }}">
          <div class="grid-inner-wrapper">
            <canvas class="curtain-canvas"></canvas>
            <div class="curtain-handle">
              <span class="curtain-arrow left">←</span>
              <span class="curtain-arrow right">→</span>
            </div>
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
             style="width: {{ $widthPercent }}%;"
             data-media-width="{{ $w }}" data-media-height="{{ $h }}">
          <div class="grid-inner-wrapper">
            {!! $iframeSrc !!}
          </div>
        </div>
        @break

      @default
        <div class="grid-item"
             style="width: {{ $widthPercent }}%;"
             data-media-width="16" data-media-height="9">
          <div class="grid-inner-wrapper">
            {{ $col['link'] ?? '' }}
          </div>
        </div>
    @endswitch
  @endforeach
</div>
  @endforeach
</div>

<script>
// 🎬 Инициализация canvas-шторки
function initCurtainCanvas(canvas, img1src, img2src) {
  const ctx = canvas.getContext('2d');
  const handle = canvas.parentElement.querySelector('.curtain-handle');

  const img1 = new Image();
  const img2 = new Image();

  let divider = 0.5;
  let dragging = false;

  const draw = () => {
    const w = canvas.width;
    const h = canvas.height;
    ctx.clearRect(0, 0, w, h);
    ctx.drawImage(img2, 0, 0, w, h);
    ctx.drawImage(img1, 0, 0, w * divider, h, 0, 0, w * divider, h);
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

  img1.onload = () => img2.complete && finalize();
  img2.onload = () => img1.complete && finalize();

  const finalize = () => {
    const w = Math.min(img1.width, img2.width);
    const h = Math.min(img1.height, img2.height);
    canvas.removeAttribute('width');
    canvas.removeAttribute('height');
    canvas.style.width = '100%';
    canvas.style.height = 'auto';
    canvas.style.aspectRatio = `${w}/${h}`;
    draw();
    updateHandle();
  };

  img1.src = img1src;
  img2.src = img2src;
}

// ✅ Один раз при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
  // Инициализация всех curtain
  document.querySelectorAll('.curtain-container').forEach(container => {
    const canvas = container.querySelector('.curtain-canvas');
    const img1 = container.getAttribute('data-img1');
    const img2 = container.getAttribute('data-img2');
    const w = parseFloat(container.getAttribute('data-media-width')) || 1920;
    const h = parseFloat(container.getAttribute('data-media-height')) || 1080;
    canvas.width = w;
    canvas.height = h;
    initCurtainCanvas(canvas, img1, img2);
  });

  // Инициализация LightGallery для изображений
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
</script>

<style>
.grid-row {
  display: flex;
  flex-wrap: nowrap;
  gap: 6px;
  margin-bottom: 6px;
  align-items: flex-start;
  width: 100%;
}

.grid-item {
  position: relative;
  background: #000;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  width: auto;
  height: auto;
  aspect-ratio: unset; /* по умолчанию */
}

.grid-item img,
.grid-item video,
.grid-item iframe,
.grid-item canvas {
  width: 100%;
  height: auto;
  object-fit: contain;
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
  position: relative;
  width: 100%;
  height: 100%;
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
  height: auto;
  object-fit: contain;
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
    height: auto; /* можно убрать или оставить для fallback */
    max-height: 100vh;
  }

  .grid-item img,
  .grid-item video,
  .grid-item iframe {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}
</style>
@endsection
