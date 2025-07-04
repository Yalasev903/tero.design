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
    <div class="grid-row" data-row-index="{{ $rowIndex }}">
      @foreach ($row as $col)
        @php
          $w = $col['width'] ?? $col['first']['width'] ?? 1920;
          $h = $col['height'] ?? $col['first']['height'] ?? 1080;
        @endphp

        @switch($col['type'])

          @case('img')
            <a href="{{ $mediaPath($col['link']) }}" class="grid-item grid-item-img js-img"
               data-media-width="{{ $w }}" data-media-height="{{ $h }}">
              <img data-src="{{ $mediaPath($col['link']) }}" alt="{{ $col['description'] ?? '' }}" class="js-grid-item-media tero-lazy-load">
            </a>
            @break

          @case('video')
            <div class="grid-item" data-media-width="{{ $w }}" data-media-height="{{ $h }}">
              <video preload="metadata" playsinline muted loop autoplay class="js-grid-item-media tero-lazy-load">
                @foreach ($col['links'] ?? [] as $source)
                  <source data-src="{{ $mediaPath($source['link']) }}" type="{{ $source['mime'] ?? 'video/mp4' }}">
                @endforeach
              </video>
            </div>
            @break

          @case('curtain')
            @php
              $isNewFormat = isset($col['images']);
              $img1 = $mediaPath($isNewFormat ? ($col['images'][0] ?? '') : ($col['first']['link'] ?? ''));
              $img2 = $mediaPath($isNewFormat ? ($col['images'][1] ?? '') : ($col['last']['link'] ?? ''));
              $width = $col['width'] ?? ($isNewFormat ? 1920 : ($col['first']['width'] ?? 1920));
              $height = $col['height'] ?? ($isNewFormat ? 1080 : ($col['first']['height'] ?? 1080));
            @endphp
            <div class="grid-item curtain-container"
                data-img1="{{ asset(ltrim($img1, '/')) }}"
                data-img2="{{ asset(ltrim($img2, '/')) }}"
                data-media-width="{{ $width }}"
                data-media-height="{{ $height }}">
              <canvas class="curtain-canvas"></canvas>
              <div class="curtain-handle">
                <span class="curtain-arrow left">←</span>
                <span class="curtain-arrow right">→</span>
              </div>
            </div>
            @break

          @case('vr')
            @php
              $iframeSrc = $col['link'];
              if (!str_starts_with($iframeSrc, '<iframe')) {
                $iframeSrc = '<iframe src="' . e($iframeSrc) . '" frameborder="0" allowfullscreen allow="xr-spatial-tracking; gyroscope; accelerometer" scrolling="no"></iframe>';
              }
            @endphp
            <div class="grid-item" data-media-width="{{ $w }}" data-media-height="{{ $h }}">
              {!! $iframeSrc !!}
            </div>
            @break

          @default
            <div class="grid-item" data-media-width="16" data-media-height="9">
              {{ $col['link'] ?? '' }}
            </div>

        @endswitch
      @endforeach
    </div>
  @endforeach
</div>

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
    const w = canvas.width;
    const h = canvas.height;
    ctx.clearRect(0, 0, w, h);

    // Нижний слой
    ctx.drawImage(img2, 0, 0, w, h);

    // Верхний слой, ограниченный divider
    ctx.drawImage(img1, 0, 0, w * divider, h, 0, 0, w * divider, h);
  };

  // Обновление положения полосы
  const updateHandle = () => {
    handle.style.left = `${divider * 100}%`;
  };

  // Движение мыши или пальца
  const onMove = e => {
    if (!dragging) return;
    const bounds = canvas.getBoundingClientRect();
    const x = (e.touches ? e.touches[0].clientX : e.clientX) - bounds.left;
    divider = Math.max(0.01, Math.min(0.99, x / bounds.width));
    updateHandle();
    draw();
  };

  // События мыши/тача
  handle.addEventListener('mousedown', () => (dragging = true));
  handle.addEventListener('touchstart', () => (dragging = true), { passive: true });
  window.addEventListener('mouseup', () => (dragging = false));
  window.addEventListener('touchend', () => (dragging = false));
  window.addEventListener('mousemove', onMove);
  window.addEventListener('touchmove', onMove, { passive: true });

  // Когда оба изображения загружены
  img1.onload = () => {
    if (!img2.complete) return;
    finalize();
  };
  img2.onload = () => {
    if (!img1.complete) return;
    finalize();
  };

  // Устанавливаем размер canvas по минимальному размеру изображений
  const finalize = () => {
    const w = Math.min(img1.width, img2.width);
    const h = Math.min(img1.height, img2.height);

    // Применяем размеры к canvas
    canvas.width = w;
    canvas.height = h;

    // Удаляем ограничения из CSS (если есть)
    canvas.style.width = '100%';
    canvas.style.height = 'auto';

    draw();
    updateHandle();
  };

  // Задаём пути к изображениям
  img1.src = img1src;
  img2.src = img2src;
}
</script>

{{-- Скрипт для расчёта размеров + просмотр изображений --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.curtain-container').forEach(container => {
    const canvas = container.querySelector('.curtain-canvas');
    const img1 = container.getAttribute('data-img1');
    const img2 = container.getAttribute('data-img2');
    const w = parseInt(container.getAttribute('data-media-width'));
    const h = parseInt(container.getAttribute('data-media-height'));

    canvas.width = w;
    canvas.height = h;

    initCurtainCanvas(canvas, img1, img2);
  });
});

window.addEventListener('load', function () {
    // 1. Инициализация шторок

  // 2. Инициализация размеров сетки
  const rows = document.querySelectorAll('.grid-row');

  rows.forEach((row, rowIndex) => {
    const items = Array.from(row.querySelectorAll('.grid-item'));
    const count = items.length;
    let ratios = [];

    if (count >= 5) {
      row.classList.add('is-compact');

      for (let i = 0; i < count; i++) {
        let weight = (rowIndex % 2 === 1 && i === 0) ? 1.6 : 1;
        const mediaW = parseFloat(items[i].getAttribute('data-media-width')) || 1920;
        const mediaH = parseFloat(items[i].getAttribute('data-media-height')) || 1080;
        const aspectRatio = mediaW / mediaH;
        ratios.push({ el: items[i], r: weight * aspectRatio });
      }

      const total = ratios.reduce((sum, r) => sum + r.r, 0);
      ratios.forEach(({ el, r }) => {
        el.style.width = `${(r / total) * 100}%`;
        el.style.aspectRatio = "16 / 9";
      });
    } else {
      for (let i = 0; i < count; i++) {
        let weight = 1.2;
        if (rowIndex % 2 === 0) {
          if (i === 0) weight = 0.9;
          if (i === 1) weight = 1.6;
        } else {
          if (i === 0) weight = 1.6;
          if (i === 1) weight = 0.9;
        }
        if (i > 1) weight = 1.0 + Math.random() * 0.2;
        ratios.push({ el: items[i], r: weight });
      }

      const total = ratios.reduce((sum, r) => sum + r.r, 0);
      ratios.forEach(({ el, r }) => {
        el.style.width = `${(r / total) * 100}%`;
      });
    }
  });

    // Автоинициализация LightGallery при загрузке
    lightGallery(document.getElementById('js-gallery'), {
    selector: '.js-img',
    download: false,
    zoom: true,
    fullScreen: true,
    share: false
    });
});
</script>

<style>
.grid-row {
  display: flex;
  align-items: stretch;
  height: auto;
  min-height: 550px;
  margin-bottom: 5px;
}

.grid-row.is-compact {
  height: auto;
  min-height: unset;
}

.grid-item {
  position: relative;
  overflow: hidden;
  background: #000;
  flex-shrink: 0;
  height: 100%;
}

.grid-item img,
.grid-item video,
.grid-item iframe {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
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
  aspect-ratio: 16 / 9; /* убираем ограничение */
}
.curtain-canvas {
  display: block;
   max-width: 100%;
  height: 100%;
}

.curtain-wrapper {
  position: relative;
  width: 100%;
  height: 550px;
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
    aspect-ratio: 16 / 9 !important;
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
