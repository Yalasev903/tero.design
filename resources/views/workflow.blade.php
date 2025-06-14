@extends('layouts.app')

@section('header_title', $seo->title ?? 'Tero Design')
@section('header_description', $seo->description ?? '')
@section('header_keywords', $seo->keywords ?? '')
@section('header_meta_title', $seo->title ?? 'Tero Design')
@section('content')
    <div class="workflow">
        <div class="workflow-container">
            <h1 class="workflow-title">Workflow</h1>
            <div class="workflow-description">{!! $workflow->col_description ?? '' !!}</div>
            <div class="workflow-player js-player">
                <img src="/multimedia/{{ $workflow->col_poster ?? '' }}"
                     alt="{{ $workflow->col_poster_alt ?? 'Workflow poster' }}"
                     class="workflow-player-poster b-lazy">
                <div class="workflow-player-play js-video-play">
                    <img src="/img/play.png" alt="play image">
                </div>
                <video controls
                       class="workflow-player-video js-video"
                       preload="metadata">
                    <source src="/multimedia/{{ $workflow->col_video ?? '' }}" type="video/mp4">
                </video>
            </div>
        </div>
        <div class="faq">
            <h2 class="workflow-title">Questions</h2>
            @foreach($faq_list as $item)
                <div class="faq-item">
                    <div class="faq-question js-question">
                        <svg class="faq-question-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.2 11.6">
                            <path d="M10.6 9.6L20.2 0l1 1-10.6 10.6L0 1l1-1 9.6 9.6z" />
                        </svg>
                        {!! $item->question !!}
                    </div>
                    <div class="faq-answer js-answer">{!! $item->answer !!}</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
