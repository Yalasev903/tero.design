@extends('layouts.app')

@section('content')
    <div class="services">
        @foreach($service_list as $service)
            <div class="services-item">
                <div class="{{ $loop->index % 2 == 0 ? 'services-item-left' : 'services-item-right' }}">
                    <h2 class="services-item-title">{{ $service->title }}</h2>
                    <p class="services-item-description">{!! $service->description !!}</p>
                </div>
                <video playsinline muted loop
                       autoplay="autoplay"
                       data-media-width="{{ $service->video['width'] ?? '' }}"
                       data-media-height="{{ $service->video['height'] ?? '' }}"
                       class="services-item-video">
                    @if(isset($service->video['links']) && is_array($service->video['links']))
                        @foreach($service->video['links'] as $link)
                            <source src="/multimedia/{{ $link['link'] }}" type="{{ $link['mime'] }}">
                        @endforeach
                    @endif
                </video>
            </div>
        @endforeach
    </div>
@endsection
