@extends('layouts.app')

@section('header_title', $seo->title ?? 'Tero Design')
@section('header_description', $seo->description ?? '')
@section('header_keywords', $seo->keywords ?? '')
@section('header_meta_title', $seo->title ?? 'Tero Design')

@section('content')
    <div class="workflow">
        <div class="workflow-container">
            <h1 class="workflow-title">Workflow</h1>

            <div class="workflow-description">
                {!! $workflow->col_description ?? '' !!}
            </div>

            <div class="workflow-player js-player">
                <div id="vimeo-container" style="display: none; position: relative; padding-top: 56.25%;">
                    <iframe src="https://player.vimeo.com/video/1015356904?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479"
                            frameborder="0"
                            allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            style="position:absolute;top:0;left:0;width:100%;height:100%;"
                            title="Terodesign Showreel 2024"
                            id="vimeo-player">
                    </iframe>
                </div>

                <img src="/multimedia/{{ $workflow->col_poster ?? '' }}"
                    id="workflow-poster"
                    alt="{{ $workflow->col_poster_alt ?? 'Workflow poster' }}"
                    class="workflow-player-poster b-lazy">

                <div class="workflow-player-play js-workflow-play">
                    <img src="/img/play.png" alt="play image">
                </div>

                <script src="https://player.vimeo.com/api/player.js"></script>
                {{-- <img src="/multimedia/{{ $workflow->col_poster ?? '' }}"
                     id="workflow-poster"
                     alt="{{ $workflow->col_poster_alt ?? 'Workflow poster' }}"
                     class="workflow-player-poster b-lazy">

                <div class="workflow-player-play js-workflow-play">
                    <img src="/img/play.png" alt="play image">
                </div>

                <video controls
                       id="workflow-video"
                       class="workflow-player-video js-workflow-video"
                       preload="metadata"
                       playsinline
                       controlsList="nodownload noplaybackrate nofullscreen"
                       disablePictureInPicture>
                    <source src="/multimedia/{{ $workflow->col_video ?? '' }}" type="video/mp4">
                </video> --}}
            </div>
        </div>

        <div class="faq">
            <h2 class="workflow-title">Questions</h2>

            @foreach($faq_list->sortBy('position') as $item)
                <div class="faq-item">
                    <div class="faq-question js-question">
                        <svg class="faq-question-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.2 11.6">
                            <path d="M10.6 9.6L20.2 0l1 1-10.6 10.6L0 1l1-1 9.6 9.6z"/>
                        </svg>
                        {!! $item->col_question !!}
                    </div>
                    <div class="faq-answer js-answer">
                        {!! $item->col_answer !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('workflow-video');
    const poster = document.getElementById('workflow-poster');
    const playBtn = document.querySelector('.js-workflow-play');

    if (!video || !playBtn || !poster) return;

    playBtn.addEventListener('click', function () {
        // Скрываем постер и кнопку
        poster.style.display = 'none';
        playBtn.style.display = 'none';

        // Останавливаем все остальные видео, кроме workflow
        document.querySelectorAll('video').forEach(v => {
            if (v !== video) {
                v.pause();
            }
        });

        // Показываем и воспроизводим текущее видео
        video.style.display = 'block';
        video.play().catch(e => console.warn('Ошибка воспроизведения:', e));
    });

    // Если пользователь поставил паузу вручную — вернуть постер
    video.addEventListener('pause', function () {
        if (!video.ended) {
            poster.style.display = 'block';
            playBtn.style.display = 'block';
            video.style.display = 'none';
        }
    });

    // При завершении — показать постер снова
    video.addEventListener('ended', function () {
        poster.style.display = 'block';
        playBtn.style.display = 'block';
        video.style.display = 'none';
        video.currentTime = 0;
    });
});
</script> --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const poster = document.getElementById('workflow-poster');
    const playBtn = document.querySelector('.js-workflow-play');
    const vimeoContainer = document.getElementById('vimeo-container');

    if (!playBtn || !poster || !vimeoContainer) return;

    let vimeoPlayer = null;

    playBtn.addEventListener('click', function () {
        // Скрыть постер и кнопку play
        poster.style.display = 'none';
        playBtn.style.display = 'none';

        // Показать iframe Vimeo
        vimeoContainer.style.display = 'block';

        // Инициализация плеера
        if (!vimeoPlayer) {
            const iframe = document.getElementById('vimeo-player');
            vimeoPlayer = new Vimeo.Player(iframe);
        }

        // Попытка воспроизведения
        vimeoPlayer.play().catch(function (error) {
            console.warn('Ошибка воспроизведения Vimeo:', error.name || error);
        });
    });
});
</script>
<script>

	/* FAQ
	------------------------------------------------------- */
	const questionElements = document.querySelectorAll('.js-question')
	if(questionElements) {
		for(const question of questionElements) {
			question.addEventListener('click', () => {
				const answer = question.nextElementSibling
				answer.classList.toggle('active')
				question.classList.toggle('active')
				return false
			})
		}
	}
</script>
@endsection
