@extends('layouts.app')

@section('title', $project->meta_title ?? $project->title)
@section('description', $project->meta_description)
@section('keywords', $project->meta_keywords)

@section('content')
<div class="project">
  <div class="project-head row2">
    <h1 class="project-title">{{ $project->title }}</h1>
    <a href="{{ route('home') }}#js-grid-item{{ $project->id }}" class="project-prev row">
      <svg class="project-prev-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M136.97 380.485..."/></svg>
      <span class="project-prev-label">back to portfolio</span>
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
            <div class="grid-item" data-media-width="{{ $w }}" data-media-height="{{ $h }}">
              <img data-src="/multimedia/{{ $col['link'] }}" alt="{{ $col['description'] ?? '' }}" class="js-grid-item-media tero-lazy-load">
            </div>
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
              $img1 = $col['first']['link'] ?? null;
              $img2 = $col['last']['link'] ?? null;
            @endphp
            <div class="grid-item curtain twentytwenty-container" data-media-width="{{ $w }}" data-media-height="{{ $h }}">
              @if ($img1 && $img2)
                <img class="tero-lazy-load twentytwenty-before" data-src="/multimedia/{{ $img1 }}">
                <img class="tero-lazy-load twentytwenty-after" data-src="/multimedia/{{ $img2 }}">
              @else
                <div class="error">⛔ Шторка не содержит изображений</div>
              @endif
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
document.addEventListener("DOMContentLoaded", function () {
  const rows = document.querySelectorAll('.grid-row');

  rows.forEach((row, rowIndex) => {
    const items = Array.from(row.querySelectorAll('.grid-item'));
    const count = items.length;
    let ratios = [];

    // Устанавливаем класс, если 5+
    if (count >= 5) {
      row.classList.add('is-compact');
    }

    for (let i = 0; i < count; i++) {
      let weight = 1.2;

      if (count >= 5) {
        weight = 1; // все равномерные квадратные
      } else {
        if (rowIndex % 2 === 0) {
          if (i === 0) weight = 0.9;
          if (i === 1) weight = 1.6;
        } else {
          if (i === 0) weight = 1.6;
          if (i === 1) weight = 0.9;
        }
        if (i > 1) weight = 1.0 + Math.random() * 0.2;
      }

      ratios.push({ el: items[i], r: weight });
    }

    const total = ratios.reduce((sum, r) => sum + r.r, 0);
    ratios.forEach(({ el, r }) => {
      el.style.width = `${(r / total) * 100}%`;
    });
  });
});
</script>

<style>
.grid-row {
  display: flex;
  /* gap: 1px; */
  margin-bottom: px;
  height: 550px; !important;
   min-height: 550px;
  align-items: stretch;
}

.grid-row.is-compact {
  height: 420px;
}

.grid-item {
  position: relative;
  overflow: hidden;
  /* height: 100%; !important; */
  /* min-height: 550px; */
  background: #000;
  flex-shrink: 0;
}

.grid-item img,
.grid-item video,
.grid-item iframe {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Curtain */
.grid-item.curtain img {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.grid-item.curtain .twentytwenty-after {
  z-index: 1;
}

.grid-item.curtain .twentytwenty-before {
  z-index: 2;
  clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);
}

/* Мобильная адаптация */
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
