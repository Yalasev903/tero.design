function checkTitleOverlap(titleElement, buttonContainer) {
  const popupInner = titleElement.closest('.popup-inner');
  const titleRect = titleElement.getBoundingClientRect();
  const buttonRect = buttonContainer.getBoundingClientRect();

  const verticalOverlap = !(titleRect.bottom < buttonRect.top || titleRect.top > buttonRect.bottom);
  const horizontalOverlap = !(titleRect.right < buttonRect.left || titleRect.left > buttonRect.right);

  const isOverlapping = verticalOverlap && horizontalOverlap;
  titleElement.style.top = isOverlapping ? '-21px' : '0';

  if (isOverlapping) {
    popupInner.style.marginTop = window.innerWidth <= 768 ? '60px' : '40px';
  } else {
    popupInner.style.marginTop = '0';
  }
}

function bindPopupEvents() {
  const gridItemLinks = document.getElementsByClassName('grid-item');
  const popup = document.createElement('div');
  popup.classList.add('popup');
  document.body.appendChild(popup);

  for (let i = 0; i < gridItemLinks.length; i++) {
    gridItemLinks[i].addEventListener('click', function (e) {
      e.preventDefault();
      const mediaType = this.classList.contains('grid-item-video') ? 'video' : 'image';
      let mediaUrl = this.dataset.image;
      const title = this.querySelector('.grid-item-title').innerText;
      const projectLink = this.dataset.projectLink;
      const text2 = this.dataset.text2;

      const popupInner = document.createElement('div');
      popupInner.classList.add('popup-inner');
      popupInner.style.position = 'relative';

      if (mediaType === 'image') {
        const img = document.createElement('img');
        img.src = mediaUrl;
        img.onload = function () {
          if (this.naturalHeight / this.naturalWidth > 1.5) {
            this.classList.add('tall-media');
            popupInner.classList.add('tall-adjust');
          }
        };
        popupInner.appendChild(img);
      } else {
        mediaUrl = this.dataset.video;
        const video = document.createElement('video');

        // ✅ Ключевые атрибуты для iOS (иначе fullscreen)
        video.setAttribute('playsinline', '');
        video.setAttribute('webkit-playsinline', '');
        video.setAttribute('muted', '');
        video.setAttribute('autoplay', '');
        video.setAttribute('loop', '');
        video.setAttribute('preload', 'metadata');

        // ✅ Дублируем на случай полной поддержки
        video.muted = true;
        video.autoplay = true;
        video.playsInline = true;

        const source = document.createElement('source');
        source.src = mediaUrl;
        video.appendChild(source);

        // ✅ Добавляем поведение при длинных видео
        video.onloadedmetadata = function () {
        if (video.videoHeight / video.videoWidth > 1.5) {
            video.classList.add('tall-media');
            popupInner.classList.add('tall-adjust');
        }
        };

        popupInner.appendChild(video);
      }

      const topBar = document.createElement('div');
      if (window.innerWidth <= 576) {
        Object.assign(topBar.style, {
          display: 'flex', flexDirection: 'row', justifyContent: 'space-between',
          alignItems: 'center', gap: '10px', width: '100%', padding: '12px', boxSizing: 'border-box'
        });
      } else {
        Object.assign(topBar.style, {
          position: 'absolute', top: '-35px', left: '10px', right: '10px', zIndex: '2', height: '30px'
        });
      }

      const titleElement = document.createElement('h3');
      titleElement.innerText = title;
      Object.assign(titleElement.style, {
        color: '#fff', margin: '0', fontSize: '20px', position: 'absolute', left: '0', top: '0'
      });

      const buttonContainer = document.createElement('div');
      Object.assign(buttonContainer.style, {
        position: 'absolute', right: '0', top: '-5px'
      });

      if (projectLink) {
        const link = document.createElement('a');
        Object.assign(link, {
          href: projectLink, target: '_blank', rel: 'noopener', className: 'project-link-text', textContent: 'View Full Project'
        });
        Object.assign(link.style, { position: 'relative', top: '-10px', marginRight: '10px' });
        buttonContainer.appendChild(link);
      }

      const infoIcon = document.createElement('span');
      infoIcon.innerHTML = '<img class="i_svg" src="/multimedia/info.svg" alt="i">';
      infoIcon.classList.add('info-button');
      infoIcon.style.cursor = 'pointer';
      infoIcon.addEventListener('click', () => {
        let infoBlock = popupInner.querySelector('.info-block');
        if (infoBlock) return popupInner.removeChild(infoBlock);
        infoBlock = document.createElement('div');
        infoBlock.classList.add('info-block');
        infoBlock.innerHTML = (text2 || '').replace(/<p>(\s|&nbsp;|\u00A0)*<\/p>/gi, '').replace(/<br\s*\/?>/gi, '').replace(/<ul>\s*<\/ul>/gi, '').replace(/\s+$/, '');
        popupInner.appendChild(infoBlock);
      });
      buttonContainer.appendChild(infoIcon);

      topBar.appendChild(titleElement);
      topBar.appendChild(buttonContainer);
      popupInner.appendChild(topBar);

      const controls = document.createElement('div');
      controls.classList.add('popup-controls');
        // Вставка SVG из Blade — как строки
        const svgArrowPrev = `
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="17" viewBox="0 0 5560.1 4209.61">
        <path fill="#fff" d="M2340.98 210.48c0,141.49 -70.98,176.24 -157.86,263.11l-1293.84 1293.84c-35.58,35.58 -82.15,62.87 -95.94,114.53l4358.18 0c118.54,0 408.58,-41.91 408.58,222.86 0,251.74 -278.74,222.86 -396.2,222.86l-4382.96 0c38.3,57.18 1250.22,1256.4 1395.98,1402.17 88.34,88.33 164.05,126.02 164.05,269.29 0,100.49 -90.44,210.48 -185.71,210.48 -186.19,0 -199.36,-56.99 -368.33,-225.95l-1560.03 -1560.05c-100.61,-100.6 -354.78,-305.2 -148.24,-488.72 284.63,-252.88 654.83,-643.11 928.25,-916.54l922.39 -922.42c69.72,-70.21 82.9,-95.94 201.2,-95.94 109.53,0 210.48,100.96 210.48,210.48z"/>
        </svg>`;

        const svgArrowNext = `
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="17" viewBox="0 0 1091.71 826.54">
        <path fill="#fff" d="M632.07 41.33c0,27.78 13.94,34.6 31,51.66l254.04 254.04c6.99,6.99 16.13,12.34 18.84,22.49l-855.72 0c-23.27,0 -80.22,-8.23 -80.22,43.76 0,49.43 54.73,43.76 77.79,43.76l860.58 0c-7.52,11.23 -245.48,246.69 -274.1,275.31 -17.35,17.34 -32.21,24.74 -32.21,52.87 0,19.73 17.76,41.33 36.46,41.33 36.56,0 39.14,-11.19 72.32,-44.36l306.31 -306.31c19.76,-19.75 69.66,-59.93 29.11,-95.96 -55.89,-49.65 -128.57,-126.27 -182.26,-179.96l-181.11 -181.11c-13.69,-13.78 -16.28,-18.84 -39.51,-18.84 -21.51,0 -41.33,19.82 -41.33,41.33z"/>
        </svg>`;

        const svgClose = `
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 960.11 960.1">
        <path fill="#fff" d="M8.07 911.53l431.48 -431.48 -431.48 -431.48c-10.76,-10.76 -10.76,-28.37 0,-39.13l1.37 -1.37c10.76,-10.76 28.37,-10.76 39.13,0l431.48 431.48 431.48 -431.48c10.76,-10.76 28.37,-10.76 39.13,0l1.37 1.37c10.76,10.76 10.76,28.37 0,39.13l-431.48 431.48 431.48 431.48c10.76,10.76 10.76,28.37 0,39.13l-1.37 1.37c-10.76,10.76 -28.37,10.76 -39.13,0l-431.48 -431.48 -431.48 431.48c-10.76,10.76 -28.37,10.76 -39.13,0l-1.37 -1.37c-10.76,-10.76 -10.76,-28.37 0,-39.13z"/>
        </svg>`;

      const btnPrev = document.createElement('div');
      btnPrev.classList.add('popup-arrow', 'prev');
      btnPrev.innerHTML = svgArrowPrev;
      btnPrev.onclick = () => gridItemLinks[(i - 1 + gridItemLinks.length) % gridItemLinks.length].click();

      const btnNext = document.createElement('div');
      btnNext.classList.add('popup-arrow', 'next');
      btnNext.innerHTML = svgArrowNext;
      btnNext.onclick = () => gridItemLinks[(i + 1) % gridItemLinks.length].click();

      const btnClose = document.createElement('div');
      btnClose.classList.add('popup-close');
      btnClose.innerHTML = svgClose;
      btnClose.onclick = () => {
        popup.innerHTML = '';
        popup.classList.remove('active');
      };

      controls.append(btnPrev, btnClose, btnNext);
      popupInner.appendChild(controls);

      setTimeout(() => checkTitleOverlap(titleElement, buttonContainer), 300);
      window.addEventListener('resize', () => {
        if (popup.classList.contains('active')) {
          checkTitleOverlap(popup.querySelector('h3'), popup.querySelector('.info-button')?.parentElement);
        }
      });

      // 🧼 Fix багов отрисовки на iOS — удаляем всё, что могло не успеть удалиться
        popup.querySelectorAll('.popup-inner').forEach(el => el.remove());
        popup.querySelectorAll('.popup-controls').forEach(el => el.remove());
        popup.querySelectorAll('h3').forEach(el => el.remove());
        popup.querySelectorAll('.project-link-text').forEach(el => el.remove());
        popup.querySelectorAll('.info-button').forEach(el => el.remove());

      popup.innerHTML = '';
      popup.appendChild(popupInner);
      popup.classList.add('active');

      const mediaEl = popupInner.querySelector('img, video');
      mediaEl?.addEventListener('click', function (e) {
        if (e.target === this) {
          popup.innerHTML = '';
          popup.classList.remove('active');
        }
      });
    });
  }

  popup.addEventListener('click', function (e) {
    if (e.target === this) {
      this.innerHTML = '';
      this.classList.remove('active');
    }
  });
}

export { bindPopupEvents }
