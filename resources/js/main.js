import 'lazysizes';
import 'lazysizes/plugins/attrchange/ls.attrchange';

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('js-showreel');
    const posterBlock = document.getElementById('showreel-poster-block');
    const video = document.getElementById('js-video');
    const page = document.getElementById('page');
    const closeBtn = document.getElementById('js-showreel-close');
    const playBtn = document.querySelector('.js-video-play');

    const openModal = () => {
        if (!modal) return;
        modal.classList.add('open');
        modal.style.display = 'flex';
        modal.style.visibility = 'visible';
        modal.style.opacity = '1';
        page?.classList.add('form-open');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    };

    const closeModal = () => {
        if (!modal) return;
        modal.classList.remove('open');
        modal.style.display = 'none';
        modal.style.visibility = 'hidden';
        modal.style.opacity = '0';
        page?.classList.remove('form-open');
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
        if (video) {
            video.pause();
            video.currentTime = 0;
            video.style.visibility = 'hidden';
            video.style.opacity = '0';
            video.style.display = 'none';
        }
        if (posterBlock) {
            posterBlock.style.visibility = 'visible';
            posterBlock.style.opacity = '1';
        }
    };

    document.querySelectorAll('.js-showreel-open').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            openModal();
        });
    });

    playBtn?.addEventListener('click', () => {
        if (!video || !posterBlock) return;

        posterBlock.style.visibility = 'hidden';
        posterBlock.style.opacity = '0';

        video.style.visibility = 'visible';
        video.style.opacity = '1';
        video.style.display = 'block';

        video.play();
    });

    closeBtn?.addEventListener('click', e => {
        e.preventDefault();
        closeModal();
    });

    modal?.addEventListener('click', e => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    document.addEventListener('lazyloaded', function (e) {
        const el = e.target;
        if (el.tagName === 'VIDEO') {
            el.querySelectorAll('source').forEach(source => {
                if (source.dataset.src && !source.src) {
                    source.src = source.dataset.src;
                }
            });
            el.load();
        }
    });
});
