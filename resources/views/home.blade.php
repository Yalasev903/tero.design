@extends('layouts.app')

@section('content')
    <div class="grid" id="js-gallery">
        @foreach($projects_grid as $row)
            <div class="grid-row" id="js-grid-item{{ $row[0]['project_id'] ?? '' }}">
                @php $i = $loop->index; @endphp
                @foreach($row as $col)
                    @php
                        $media = json_decode($col['media'], true);
                    @endphp
                    <a href="#"
                        data-image="/multimedia/{{ $media['link'] ?? '' }}"
                        data-video="@if(($media['type'] ?? '') == 'video')/multimedia/{{ $media['links'][0]['link'] ?? '' }}@endif"
                        id="grid-el-{{ $i }}-{{ $loop->index }}"
                        class="grid-item loading {{ ($col['is_mobile'] ?? false) ? 'grid-item-mobile' : 'grid-item-desktop' }} grid-item-{{ $media['type'] ?? '' }}"
                        data-media-width="{{ $media['width'] ?? '' }}"
                        data-media-height="{{ $media['height'] ?? '' }}"
                        data-project-link="{{ route('projects.show', ['id' => $col['project_id']]) }}"
                        data-text2="{{ $col['text2'] ?? '' }}">
                        @if(($media['type'] ?? '') === 'img')
                            <img data-src="/multimedia/{{ $media['link'] ?? '' }}"
                                alt="{{ $media['alt'] ?? '' }}"
                                class="js-grid-item-media tero-lazy-load"
                            >
                            <h3 class="grid-item-title">{{ $col['title'] ?? '' }}</h3>
                        @elseif(($media['type'] ?? '') === 'video')
                            <video preload="metadata" muted loop autoplay class="js-grid-item-media tero-lazy-load">
                                @foreach($media['links'] ?? [] as $link)
                                    <source data-src="/multimedia/{{ $link['link'] ?? '' }}" type="{{ $link['mime'] ?? '' }}">
                                @endforeach
                            </video>
                            <h3 class="grid-item-title">{{ $col['title'] ?? '' }}</h3>
                        @endif
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
    <div id="scroll-to-top">
        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGhlaWdodD0iNTEycHgiIGlkPSJMYXllcl8xIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgdmVyc2lvbj0iMS4xIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgd2lkdGg9IjUxMnB4IiB4bWxzOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxucz
0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PHBhdGggZD0iTTEyOC40LDE4OS4zTDIzMy40LDg5YzUuOC02LDEzLjctOSwyMi40LTljOC43LDAsMTYuNSwzLDIyLjQsOWwxMDUuNCwxMDAuM2MxMi41LDExLjksMTIuNSwzMS4zLDAsNDMuMiAgYy0xMi41LDExLjktMzIuNywxMS45LTQ1LjIsMEwyODgsMTg0LjR2MjE3YzAsMTYuOS0xNC4zLDMwLjYtMzIsMzAuNmMtMTcuNywwLTMyLTEzLjctMzItMzAuNnYtMjE3bC01MC40LDQ4LjIgIC0xMi41LDExLjktMzIuNywxMS45LTQ1LjIsMEMxMTUuOSwyMjAuNiwxMTUuOSwyMDEuMywxMjguNCwxODkuM3oiLz48L3N2Zz4=">
    </div>

    {{-- Внутренние стили главной --}}
    <style>
        .popup { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 9999; text-align: center;}
        .popup img, .popup video { max-width: 100%; max-height: 100%; order: 1;}
        .popup .grid-item-title { color: white; font-size: 24px; margin-top: 10px;}
        .popup.active { display: flex; justify-content: center; align-items: center; flex-flow: column;}
        .popup a { order: 2; margin-top: 10px; margin-bottom: -10px;}
        .popup-inner { max-width: 80%; max-height: 80%; display: flex; align-items: center; justify-content: center; flex-flow: column; position: relative;}
        .popup .project-info { display: flex; flex-flow: row-reverse; align-items: center; justify-content: space-between; width: 100%; margin-bottom: 5px; margin-top: -5px;}
        .popup .i_svg { width: 32px; cursor: pointer;}
        .info-block { position: absolute; right: 16px; top: 34px; background: #0009; padding: 16px 16px; text-align: left; max-width: 330px;}
        .info-block .sub-title, .info-block span.bold, .info-block span.texteditor-inline-fontsize, .info-block p, .info-block ul li { font-size: 14px; line-height: 125%;}
        .info-block a { text-decoration: underline;}
        @media (max-width: 991px) { .info-block { top: 50px; }}
        @media (max-width: 576px) {
            .popup-inner { max-width: 95%; max-height: 95%;}
            .info-block ul li { font-size: 12px;}
            .info-block { right: 11px; top: 45px; background: #0009; padding: 8px 8px; text-align: left;}
        }
        .close_svg { position: absolute; right: 10px; top: 26px; width: 28px; cursor: pointer;}
    </style>
    {{-- / Внутренние стили главной --}}

    {{-- JS для popup --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var gridItemLinks = document.getElementsByClassName('grid-item');
            var popup = document.createElement('div');
            popup.classList.add('popup');
            document.body.appendChild(popup);

            for (var i = 0; i < gridItemLinks.length; i++) {
                gridItemLinks[i].addEventListener('click', function(e) {
                    e.preventDefault();
                    var mediaType = this.classList.contains('grid-item-video') ? 'video' : 'image';
                    var mediaUrl = this.dataset.image;
                    var title = this.querySelector('.grid-item-title').innerText;
                    var projectLink = this.dataset.projectLink;
                    var text2 = this.dataset.text2;

                    if (mediaType === 'image') {
                        mediaUrl = this.dataset.image;
                        var popupInner = document.createElement('div');
                        popupInner.classList.add('popup-inner');
                        var img = document.createElement('img');
                        img.src = mediaUrl;
                        popupInner.appendChild(img);

                        var projectInfo = document.createElement('div');
                        projectInfo.classList.add('project-info');
                        var titleElement = document.createElement('h3');
                        titleElement.innerText = title;

                        var infoIcon = document.createElement('svg');
                        infoIcon.innerHTML = '<img class="i_svg" src="/multimedia/info.svg" alt="">';
                        infoIcon.addEventListener('click', function() {
                            var infoBlock = projectInfo.querySelector('.info-block');
                            if (infoBlock) {
                                projectInfo.removeChild(infoBlock);
                            } else {
                                infoBlock = document.createElement('div');
                                infoBlock.classList.add('info-block');
                                infoBlock.innerHTML = text2;
                                projectInfo.appendChild(infoBlock);
                            }
                        });

                        projectInfo.appendChild(infoIcon);
                        projectInfo.appendChild(titleElement);
                        popupInner.appendChild(projectInfo);

                        popup.innerHTML = '';
                        popup.appendChild(popupInner);
                        popup.classList.add('active');

                        img.addEventListener('click', function(e) {
                            if (e.target === this) {
                                popup.innerHTML = '';
                                popup.classList.remove('active');
                            }
                        });
                    } else if (mediaType === 'video') {
                        mediaUrl = this.dataset.video;
                        var popupInner = document.createElement('div');
                        popupInner.classList.add('popup-inner');
                        var video = document.createElement('video');
                        video.preload = 'metadata';
                        video.muted = true;
                        video.loop = true;
                        video.autoplay = true;
                        var source = document.createElement('source');
                        source.src = mediaUrl;
                        video.appendChild(source);
                        popupInner.appendChild(video);

                        var projectInfo = document.createElement('div');
                        projectInfo.classList.add('project-info');
                        var titleElement = document.createElement('h3');
                        titleElement.innerText = title;

                        var infoIcon = document.createElement('svg');
                        infoIcon.innerHTML = '<img class="i_svg" src="/multimedia/info.svg" alt="">';
                        infoIcon.addEventListener('click', function() {
                            var infoBlock = projectInfo.querySelector('.info-block');
                            if (infoBlock) {
                                projectInfo.removeChild(infoBlock);
                            } else {
                                infoBlock = document.createElement('div');
                                infoBlock.classList.add('info-block');
                                infoBlock.innerHTML = text2;
                                projectInfo.appendChild(infoBlock);
                            }
                        });

                        projectInfo.appendChild(infoIcon);
                        projectInfo.appendChild(titleElement);
                        popupInner.appendChild(projectInfo);

                        popup.innerHTML = '';
                        popup.appendChild(popupInner);
                        popup.classList.add('active');

                        video.addEventListener('click', function(e) {
                            if (e.target === this) {
                                popup.innerHTML = '';
                                popup.classList.remove('active');
                            }
                        });
                    }
                });
            }

            popup.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.innerHTML = '';
                    this.classList.remove('active');
                }
            });
        });
    </script>
    {{-- / JS для popup --}}
@endsection
