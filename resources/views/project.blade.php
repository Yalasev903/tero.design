@extends('layouts.app')

@section('title', $project->meta_title ?? $project->title)
@section('description', $project->meta_description)
@section('keywords', $project->meta_keywords)

@section('content')
<div class="project">
  <div class="project-head row2">
    <h1 class="project-title">{{ $project->title }}</h1>
    <a href="{{ route('home') }}#js-grid-item{{ $project->id }}" class="project-prev row">
      <svg class="project-prev-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
        <path d="M136.97 380.485l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L60.113 273H436c6.627 0 12-5.373 12-12v-10c0-6.627-5.373-12-12-12H60.113l83.928-83.444c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0l-116.485 116c-4.686 4.686-4.686 12.284 0 16.971l116.485 116c4.686 4.686 12.284 4.686 16.97-.001z"></path>
      </svg>
      <span class="project-prev-label">back to portfolio</span>
    </a>
  </div>

  <div class="project-description">
    <div class="project-description-col">{!! $project->text1 !!}</div>
    <div class="project-description-col">{!! $project->text2 !!}</div>
  </div>
</div>

<div class="grid" id="js-gallery">
  @php
    $grid = $project->multimedia_grid;
  @endphp

  @foreach ($grid as $row)
    <div class="grid-row">
      @foreach ($row as $col)
        @switch($col['type'])

          @case('img')
            <a href="/multimedia/{{ $col['link'] }}" class="grid-item grid-item-img js-img loading"
               data-media-width="{{ $col['width'] ?? '' }}" data-media-height="{{ $col['height'] ?? '' }}">
              <img data-src="/multimedia/{{ $col['link'] }}" alt="{{ $col['description'] ?? '' }}"
                   class="js-grid-item-media tero-lazy-load">
            </a>
            @break

          @case('video')
            <div class="grid-item loading"
                 data-media-width="{{ $col['width'] ?? '' }}" data-media-height="{{ $col['height'] ?? '' }}">
              <video preload="metadata" playsinline muted loop autoplay class="js-grid-item-media tero-lazy-load">
                @foreach ($col['links'] ?? [] as $source)
                  <source data-src="/multimedia/{{ $source['link'] }}" type="{{ $source['mime'] ?? 'video/mp4' }}">
                @endforeach
              </video>
            </div>
            @break

          @case('curtain')
            <div class="grid-item curtain loading curtain-left"
                 data-media-width="{{ $col['first']['width'] ?? '' }}" data-media-height="{{ $col['first']['height'] ?? '' }}">
              <img class="tero-lazy-load" data-src="/multimedia/{{ $col['images'][0] ?? '' }}" alt="Curtain Front">
              <img class="tero-lazy-load" data-src="/multimedia/{{ $col['images'][1] ?? '' }}" alt="Curtain Back">
            </div>
            @break

          @case('vr')
            <div class="grid-item grid-item-360"
                 data-media-width="{{ $col['width'] ?? 800 }}" data-media-height="{{ $col['height'] ?? 600 }}">
              {!! $col['link'] !!}
            </div>
            @break

          @default
            <div class="grid-item">{{ $col['link'] ?? '' }}</div>

        @endswitch
      @endforeach
    </div>
  @endforeach
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const projectDescription = document.querySelector(".project-description");
    if (projectDescription) {
      const links = projectDescription.querySelectorAll("li");
      links.forEach(function(li) {
        const link = li.querySelector("a");
        if (link && link.textContent.trim() === "View Full Project") {
          li.remove();
        }
      });
    }
  });
</script>
@endsection
