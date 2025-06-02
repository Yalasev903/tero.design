$(function() {
	$.extend($.lazyLoadXT, {
		srcAttr: '[data-src]',
		forceLoad: true
	});

	/* Helpers
	------------------------------------------------------- */
	function delegate(el, evt, sel, handler) {
		el.addEventListener(evt, function(event) {
			let t = event.target;
			while (t && t !== this) {
				if (t.matches(sel)) {
					handler.call(t, event);
				}
				t = t.parentNode;
			}
		});
	}
	// delegate(document, "click", ".js-video-play", event => { });
	Document.prototype.delegate = function(selector, handler) {
		const elements = this.querySelectorAll(selector)
		for(const element of elements) {
			handler(element)
		}
	}
	Element.prototype.delegate = Document.prototype.delegate
	document.delegate('.organic__url-text', el => el.style.opacity = 0)

	Element.prototype.parent = function(selector) {
		let elem = this;
		let isHaveSelector = selector !== undefined;

		while ((elem = elem.parentElement) !== null) {
			if (elem.nodeType !== Node.ELEMENT_NODE) {
				continue;
			}

			if (!isHaveSelector || elem.matches(selector)) {
				return elem
			}
		}

		return undefined;
	};

	/* Grid
	------------------------------------------------------- */
	window.onload = () => {
		let curtains = document.querySelectorAll('.curtain')
		for(let curtain of curtains) {
			// let curtain_orientation = (curtain.classList.contains('curtain-v')) ? 'vertical' : 'horizontal';
			$(curtain).twentytwenty({
				before_label: '',
				after_label : '',
				orientation : 'horizontal'
			});
		}
	}

	/* Player size
	------------------------------------------------------- */
	const videoElement = document.querySelector('#js-video')
	const playerElement = document.querySelector('#js-player')
	if(videoElement && playerElement) {
		const resizeVideo = () => {
			let windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

			if(windowWidth < 1024) {
				let width = playerElement.offsetWidth;
				let height = width / (16 / 9);
				console.log(width, height)
				videoElement.style.height = height + 'px'
				playerElement.style.height = height + 'px'
			} else {
				videoElement.style.height = 'calc(100vh - 120px)'
				playerElement.style.height = 'auto'
			}
		}
		resizeVideo()
		window.addEventListener('resize', function(event){
			resizeVideo()
		});
	}

	/* Share
	------------------------------------------------------- */
	const shareToggle = document.querySelector('#js-share-toggle')
	const sharePopup = document.querySelector('#js-share-popup')
	if(shareToggle && sharePopup) {
		shareToggle.addEventListener('click', () => {
			shareToggle.classList.toggle('open')
			sharePopup.classList.toggle('open')
		})
	}


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

	/* Current time
	------------------------------------------------------- */
	let timeElement = document.querySelector('#js-time')
	if(timeElement) {
		let time = new Date(timeElement.textContent)
		let options = {
			month: 'long',
			day: 'numeric',
			timezone: 'UTC',
			hour: 'numeric',
			minute: 'numeric',
			second: 'numeric',
			hour12: false
		};
		setInterval(() => {
			time.setSeconds(time.getSeconds() + 1);
			timeElement.textContent = time.toLocaleString("en-US", options)
		}, 1000)
	}

	/* Showreel
	------------------------------------------------------- */
	const pageElement = document.querySelector('#page')
	const showreelOpenElements = document.querySelectorAll('.js-showreel-open')
	const showreelJsOpenElement = document.querySelector('#js-showreel')
	if(showreelOpenElements) {
		for(let showreelOpenElement of showreelOpenElements){
			showreelOpenElement.addEventListener('click', () => {
				showreelJsOpenElement.classList.add('open')
				pageElement.classList.add('form-open')
				mobileMenuClose()
			})
		}
	}

	delegate(pageElement, "click", "#js-showreel-close", () => {
		showreelClose();
	});

	function showreelClose() {
		videoElement.pause()
		videoElement.currentTime = 0
		playerElement.classList.remove('play');
		showreelJsOpenElement.classList.remove('open');
		pageElement.classList.remove('form-open');
	}


	/* Video
	------------------------------------------------------- */
	const videoPlayElement = document.querySelector('#js-video-play')
	if(videoPlayElement) {
		videoPlayElement.addEventListener('click', () => {
			videoElement.play()
			playerElement.classList.add('play')
		})
	}

	delegate(document, "click", ".js-video-play", event => {
		const player = event.target.parent('.js-player')
		if(player){
			player.classList.add('play')
		}
		const video = player.querySelector('.js-video')
		if(video) {
			video.play()
		}
	});

	/* Menu on mobile devices
	------------------------------------------------------- */
	const menuTriggerElement = document.querySelector('#js-menu-trigger')
	const mobileMenuElement = document.querySelector('#js-mobile-menu')
	const headerElement = document.querySelector('#js-header')
	if(menuTriggerElement) {
		menuTriggerElement.addEventListener('click', () => {
			menuTriggerElement.classList.toggle('open')
			mobileMenuElement.classList.toggle('open')
			headerElement.classList.toggle('fixed')
		})
	}
	function mobileMenuClose() {
		if(!menuTriggerElement)
			return
		menuTriggerElement.classList.remove('open');
		mobileMenuElement.classList.remove('open');
		headerElement.classList.remove('fixed');
	}


	/* Privacy Policy
	------------------------------------------------------- */
	const privacyPolicyCloseElement = document.querySelector('#js-privacy-policy-close')
	if(privacyPolicyCloseElement) {
		privacyPolicyCloseElement.addEventListener('click', () => {
			document.cookie = "privacy_policy=1"
			const pe = document.querySelector('#js-privacy-policy')
			pe.style.opacity = 0
			setTimeout(() => {
				pe.style.display = 'none'
			}, 350)
		})
	}

	$('#js-gallery').lightGallery({
		getCaptionFromTitleOrAlt: false,
		appendSubHtmlTo: '.lg-item',
		selector: '.js-img',
		download: false,
		counter: false,
		prevHtml: '<svg viewBox="0 0 914 534" xmlns="http://www.w3.org/2000/svg"><path d="M914 267C914 288.025 896.931 305.126 875.944 305.126H129.932L293.443 468.938C308.273 483.796 308.273 507.998 293.443 522.856C278.612 537.714 254.454 537.714 239.623 522.856L11.0997 293.913C7.5553 290.362 4.75707 286.25 2.89157 281.578C1.02607 276.905 1.75086e-05 272.046 1.75086e-05 267.187C1.75086e-05 267.093 1.75086e-05 267.093 1.75086e-05 267C1.75086e-05 262.047 1.02605 257.095 2.98482 252.422C4.85032 247.75 7.74183 243.545 11.193 240.087L239.716 11.1435C254.547 -3.7145 278.705 -3.7145 293.536 11.1435C308.367 26.0015 308.367 50.2041 293.536 65.0621L130.025 228.874H875.944C896.931 228.874 914 245.975 914 267Z" fill="#fff"/></svg>',
		nextHtml: '<svg viewBox="0 0 914 534" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 267C0 288.025 17.0693 305.126 38.0561 305.126H784.068L620.557 468.938C605.727 483.796 605.727 507.998 620.557 522.856C635.388 537.714 659.546 537.714 674.377 522.856L902.9 293.913C906.445 290.362 909.243 286.25 911.108 281.578C912.974 276.905 914 272.046 914 267.187C914 267.093 914 267.093 914 267C914 262.047 912.974 257.095 911.015 252.422C909.15 247.75 906.258 243.545 902.807 240.087L674.284 11.1435C659.453 -3.7145 635.295 -3.7145 620.464 11.1435C605.633 26.0015 605.633 50.2041 620.464 65.0621L783.975 228.874H38.0561C17.0693 228.874 0 245.975 0 267Z" fill="#fff"/></svg>'
	});

	const scrollTop = $('#scroll-to-top')
	scrollTop.on('click', () => {
		$('html,body').stop().animate({ scrollTop: $('.wrap-header').offset().top }, 1000);
		e.preventDefault();
	})

	let block_show = false;
	function scrollTracking(){
		const winScrollTop = $(window).scrollTop()
		if (block_show) {
			if(winScrollTop < 300) {
				scrollTop.hide('fast')
			}
			block_show = false
		} else {
			if(winScrollTop > 300) {
				scrollTop.show('fast')
			}
			block_show = true
		}
	}

	$(window).scroll(function(){
		scrollTracking();
	});
	scrollTop.hide()
});