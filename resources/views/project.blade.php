@extends('layouts.app')

@section('title', $project->meta_title ?? $project->title)
@section('description', $project->meta_description)
@section('keywords', $project->meta_keywords)

@section('content')
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
            <a href="/multimedia/{{ $col['link'] }}" class="grid-item grid-item-img js-img"
               data-media-width="{{ $w }}" data-media-height="{{ $h }}">
              <img data-src="/multimedia/{{ $col['link'] }}" alt="{{ $col['description'] ?? '' }}" class="js-grid-item-media tero-lazy-load">
            </a>
            @break

          @case('video')
            <div class="grid-item" data-media-width="{{ $w }}" data-media-height="{{ $h }}">
              <video preload="metadata" playsinline muted loop autoplay class="js-grid-item-media tero-lazy-load">
                @foreach ($col['links'] ?? [] as $source)
                  <source data-src="/multimedia/{{ $source['link'] }}" type="{{ $source['mime'] ?? 'video/mp4' }}">
                @endforeach
              </video>
            </div>
            @break

@case('curtain')
    @php
        $img1 = '/multimedia/' . $col['first']['link'];
        $img2 = '/multimedia/' . $col['last']['link'];
        $width = $col['first']['width'] ?? 1920;
        $height = $col['first']['height'] ?? 1080;
    @endphp

    <div class="grid-item curtain-container" data-media-width="{{ $width }}" data-media-height="{{ $height }}">
        <div class="curtain-wrapper">
            <img src="{{ $img1 }}" alt="before" class="curtain-img curtain-before">
            <img src="{{ $img2 }}" alt="after" class="curtain-img curtain-after">
            <div class="curtain-handle"></div>
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

{{-- Скрипт для расчёта размеров + просмотр изображений --}}
<script>
window.addEventListener('load', function () {
    // 1. Инициализация шторок
    document.querySelectorAll('.curtain-wrapper').forEach(wrapper => {
        const handle = wrapper.querySelector('.curtain-handle');
        const afterImg = wrapper.querySelector('.curtain-after');

        const drag = e => {
            const bounds = wrapper.getBoundingClientRect();
            let posX = ((e.clientX || e.touches[0].clientX) - bounds.left);
            posX = Math.max(0, Math.min(bounds.width, posX));
            const percent = posX / bounds.width * 100;
            afterImg.style.clipPath = `inset(0 0 0 ${percent}%)`;
            handle.style.left = `${percent}%`;
        };

        let dragging = false;
        handle.addEventListener('mousedown', () => dragging = true);
        handle.addEventListener('touchstart', () => dragging = true);
        window.addEventListener('mousemove', e => dragging && drag(e));
        window.addEventListener('touchmove', e => dragging && drag(e));
        window.addEventListener('mouseup', () => dragging = false);
        window.addEventListener('touchend', () => dragging = false);
    });

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

.before-after-wrapper {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.curtain-container {
    position: relative;
    overflow: hidden;
}
.curtain-wrapper {
    position: relative;
    width: 100%;
    height: 550px;
    aspect-ratio: 16/9;
    max-height: 80vh;
}
.curtain-img {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    object-fit: cover;
    pointer-events: none;
}
.curtain-after {
    clip-path: inset(0 0 0 50%);
    transition: clip-path 0.3s;
}
.curtain-handle {
    position: absolute;
    top: 0;
    left: 50%;
    width: 4px;
    height: 100%;
    background: rgba(255,255,255,0.8);
    cursor: ew-resize;
    z-index: 5;
}

/* Mobile */
@media (max-width: 768px) {
  .grid-row {
    flex-direction: column;
    height: auto;
  }

  .grid-item {
    width: 100% !important;
    height: auto !important;
    aspect-ratio: 16 / 9;
  }
}
</style>
@endsection
