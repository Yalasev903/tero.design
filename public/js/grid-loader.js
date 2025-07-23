(function () {
    const loader = document.querySelector('.loader');
    const grid = document.getElementById('js-gallery');
    const loadingVideo = document.getElementById('loading-video-banner');

    const showItemsSequentially = (items, index = 0) => {
        if (index >= items.length) return;
        const item = items[index];
        item.classList.add('visible');

        setTimeout(() => {
            requestAnimationFrame(() => {
                showItemsSequentially(items, index + 1);
            });
        }, 80);
    };

    const revealGrid = () => {
        if (grid) {
            grid.style.opacity = '1';
            const items = grid.querySelectorAll('.grid-item');
            showItemsSequentially(items);
        }
    };

    const hideLoader = () => {
        if (!loader) return;
        loader.style.opacity = '0';
        loader.style.pointerEvents = 'none';
        setTimeout(() => loader.remove(), 600);
        document.body.classList.remove('loading');
    };

    const init = () => {
        revealGrid();
        resizeGrid();
        observeNewGridItems();

        if (loadingVideo && loadingVideo.readyState >= 3) {
            loadingVideo.addEventListener('ended', hideLoader);
            setTimeout(hideLoader, 3600);
        } else {
            setTimeout(hideLoader, 1000);
        }
    };

    if (document.readyState === 'complete') {
        init();
    } else {
        window.addEventListener('load', init);
    }

    // Важно: повторный рендер при resize
    window.addEventListener('resize', resizeGrid);
})();

function resizeGrid() {
    const rows = document.querySelectorAll('.grid-row');
    const windowWidth = window.innerWidth;

    if (windowWidth > 1024) {
        for (let row of rows) {
            if (row.classList.contains('item-hidden')) row.classList.remove('item-hidden');
            let cols = row.querySelectorAll('.grid-item');
            if (!cols.length) continue;

            let colsArr = Array.from(cols);
            let colHeightMin = Math.min(...colsArr.map(o => parseFloat(o.dataset.mediaHeight || 1)));
            let colsWidth = 0;

            for (let col of cols) {
                let w = (colHeightMin / col.dataset.mediaHeight) * col.dataset.mediaWidth;
                colsWidth += w;
            }

            let coef = document.body.clientWidth / colsWidth;
            for (let col of cols) {
                col.style.width = ((colHeightMin / col.dataset.mediaHeight) * col.dataset.mediaWidth) * coef + 'px';
                if (col.classList.contains('grid-item-360')) {
                    col.classList.add('iframe');
                }
                col.classList.remove('item-hidden');
            }

            row.style.height = colHeightMin * coef + 'px';
        }
    } else {
        for (let row of rows) {
            row.style.height = 'auto';
            let cols = row.querySelectorAll('.grid-item');
            let hiddenCount = 0;

            for (let col of cols) {
                col.style.width = '100%';
                if (col.classList.contains('grid-item-desktop')) {
                    col.classList.add('item-hidden');
                    hiddenCount++;
                }
            }

            if (hiddenCount === cols.length) {
                row.classList.add('item-hidden');
            }
        }
    }
}

function observeNewGridItems() {
    if (!window.gridObserver) {
        window.gridObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const video = entry.target;
                if (entry.isIntersecting) {
                    if (video.readyState >= 2) {
                        video.play().catch(() => {});
                    }
                } else {
                    video.pause();
                }
            });
        }, { threshold: 0.5 });
    }

    document.querySelectorAll('video.js-grid-item-media:not([data-observed])').forEach(video => {
        window.gridObserver.observe(video);
        video.dataset.observed = 'true';
    });
}
