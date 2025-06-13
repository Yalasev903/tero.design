@extends('layouts.app')

@section('header_title', $seo->col_meta_title ?? 'Услуги - Tero Design')
@section('header_description', $seo->col_meta_description ?? 'Описание страницы услуг')
@section('header_keywords', $seo->col_meta_keywords ?? 'ключевые, слова, по, умолчанию')

@section('content')
    <div class="services">
        @foreach($service_list->sortBy('position') as $service)
            <div class="services-item">
                <div class="{{ $loop->index % 2 == 0 ? 'services-item-left' : 'services-item-right' }}">
                    <h2 class="services-item-title">{{ $service->col_title }}</h2>
                    <p class="services-item-description">{{ $service->col_description }}</p>
                </div>
                <video playsinline muted loop autoplay class="services-item-video"
                       data-media-width="" data-media-height="">
                    <source src="/multimedia/{{ $service->col_video }}" type="video/mp4">
                    Ваш браузер не поддерживает видео.
                </video>
            </div>
        @endforeach
    </div>
@endsection
