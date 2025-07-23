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
        loader.style.opacity = '0';
        loader.style.pointerEvents = 'none';
        setTimeout(() => loader.remove(), 600);
        document.body.classList.remove('loading');
    };

    const init = () => {
        revealGrid(); // 👈 Показываем сетку СРАЗУ

        if (loadingVideo && loadingVideo.readyState >= 3) {
            loadingVideo.addEventListener('ended', hideLoader);
            setTimeout(hideLoader, 3600); // запас на 1 сек
        } else {
            setTimeout(hideLoader, 1000);
        }
    };

    if (document.readyState === 'complete') {
        init();
    } else {
        window.addEventListener('load', init);
    }
})();

function resizeGrid() {
    let windowWidth = document.body.clientWidth || document.documentElement.clientWidth || window.innerWidth;

    let rows = document.querySelectorAll('.grid-row');
    if (windowWidth > 1024) {
        for (let row of rows) {
            if (row.classList.contains('item-hidden')) row.classList.remove('item-hidden');
            let cols = row.querySelectorAll('.grid-item');
            let colsArr = Array.prototype.slice.call(cols);
            let colHeightMin = Math.min.apply(Math, colsArr.map(o => o.dataset.mediaHeight));
            let colsWidth = 0;
            for (let col of cols) {
                let w = (colHeightMin / col.dataset.mediaHeight) * col.dataset.mediaWidth;
                colsWidth += w;
            }
            let cof = document.body.clientWidth / colsWidth;
            for (let col of cols) {
                col.style.width = ((colHeightMin / col.dataset.mediaHeight) * col.dataset.mediaWidth) * cof + 'px';
                if (col.classList.contains('grid-item-360')) {
                    col.classList.add('iframe');
                }
                if (col.classList.contains('item-hidden')) col.classList.remove('item-hidden');
            }
            row.style.height = colHeightMin * cof + 'px';
        }
    } else {
        for (let row of rows) {
            row.style.height = 'auto';
            let cols = row.querySelectorAll('.grid-item');
            let containsDesktopCount = 0;
            for (let col of cols) {
                col.style.width = '100%';
                if (col.classList.contains('grid-item-desktop')) {
                    col.classList.add('item-hidden');
                    containsDesktopCount++;
                }
            }
            if (containsDesktopCount === cols.length) {
                row.classList.add('item-hidden');
            }
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    resizeGrid();
    observeNewGridItems();
    window.addEventListener('resize', resizeGrid);
});
