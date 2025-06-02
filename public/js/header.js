document.addEventListener("DOMContentLoaded", () => {
	console.log('' +
		'████████╗███████╗██████╗  █████╗ \n' +
		'╚══██╔══╝██╔════╝██╔══██╗██╔══██╗\n' +
		'░░░██║░░░█████╗░░██████╔╝██║░░██║\n' +
		'░░░██║░░░██╔══╝░░██╔══██╗██║░░██║\n' +
		'░░░██║░░░███████╗██║░░██║╚█████╔╝\n' +
		'░░░╚═╝░░░╚══════╝╚═╝░░╚═╝░╚════╝░')

	/* Grid
    ------------------------------------------------------- */
	function resizeGrid() {
		let windowWidth = document.body.clientWidth || document.documentElement.clientWidth || window.innerWidth;

		let rows        = document.querySelectorAll('.grid-row')
		if( windowWidth > 1024 ) {
			for(let row of rows)
			{
				if(row.classList.contains('item-hidden'))
					row.classList.remove('item-hidden')
				let cols      = row.querySelectorAll('.grid-item')
				let colsArr   = Array.prototype.slice.call(cols)
				let colHeightMin = Math.min.apply(Math, colsArr.map(function(o) { return o.dataset.mediaHeight; }))
				let colsWidth = 0
				for(let col of cols) {
					let w = (colHeightMin/col.dataset.mediaHeight) * col.dataset.mediaWidth
					colsWidth += w
				}
				let cof = document.body.clientWidth/colsWidth
				for(let col of cols) {
					col.style.width = ((colHeightMin/col.dataset.mediaHeight) * col.dataset.mediaWidth) * cof + 'px'
					if(col.classList.contains('grid-item-360'))
					{
						col.classList.add('iframe')
					}
					if(col.classList.contains('item-hidden'))
						col.classList.remove('item-hidden')
				}
				row.style.height = colHeightMin * cof + 'px'
			}
		} else {
			for(let row of rows) {
				row.style.height = 'auto'
				let cols = row.querySelectorAll('.grid-item')
				let containsDesktopCount = 0
				for(let col of cols) {
					col.style.width = '100%'
					if(col.classList.contains('grid-item-desktop')) {
						col.classList.add('item-hidden')
						containsDesktopCount++
					}
				}
				if(containsDesktopCount === cols.length) {
					row.classList.add('item-hidden')
				}
			}
		}
	}
	document.getElementById('page').classList.remove('loading')
	document.querySelector('header').classList.add('load-complete')
	resizeGrid()
	window.addEventListener('resize', function(event){
		resizeGrid()
	});

	/* Lazy load
    ------------------------------------------------------- */
	let lazyElement = Array.prototype.slice.call(document.querySelectorAll('.tero-lazy-load'))
	function compite(obj, isError = false) {
		return () => {
			let el = obj.closest('.loading')
			if(el) el.classList.remove('loading');
			// if(el) console.log(el.id, lazyElement.length)
			let a = lazyElement.shift(); if(a) runHandle(a)
		}
	}
	for(let el of lazyElement) {
		el.addEventListener('load', compite(el))
		el.addEventListener('loadedmetadata', compite(el))
		el.addEventListener('error', compite(el, true))
	}

	if(lazyElement.length > 0)
	{
		let a = lazyElement.shift(); if(a) runHandle(a)
		a = lazyElement.shift(); if(a) runHandle(a)
		a = lazyElement.shift(); if(a) runHandle(a)
	}
	function runHandle(obj) {
		if(obj.nodeName === 'IMG') {
			obj.src = obj.dataset.src
			delete obj.dataset.src
		} else if(obj.nodeName === 'VIDEO') {
			obj.querySelectorAll('source').forEach((e) => {
				e.src = e.dataset.src
				delete e.dataset.src
			})
			obj.load()
		}
	}

	let videoHeaderEl = document.querySelector('.header-logo-img')
	let videoLoaderEl = document.querySelector('#loading-video-banner')
	let loaderEl = document.querySelector('.loader')
	if(document.referrer) {
		let refURL = new URL(document.referrer)
		if(refURL.host.endsWith('tero.design')) {
			loaderEl.classList.add('complete20')
			videoLoaderEl.addEventListener("timeupdate", function(){
				console.log(this.currentTime)
				if(this.currentTime >= 0.7) {
					this.pause();
					loaderEl.classList.add('complete')
				}
			});
			// loaderEl.classList.add('complete')
		}
	}

	videoLoaderEl.addEventListener('ended', () => {
		loaderEl.classList.add('complete')
	})

	setTimeout(() => {
		loaderEl.classList.add('complete')
	}, 2500);

	const removeVideoAddImageLoader = () => {
	    let img = document.createElement("img");
	    img.src = '/logo/loader.16.gif'
	    img.id = 'loading-video-banner'
	    loaderEl.append(img)
	    videoLoaderEl.remove()
	}

	const removeVideoAddImageHeader = () => {
	    let img = document.createElement("img");
	    img.src = '/logo/small.16.gif'
	    img.classList.add('header-logo-img')
	    document.querySelector('.header-logo').append(img)
	    videoHeaderEl.remove()
	}

	videoLoaderEl.addEventListener('autostartNotAllowed', () => {
	    removeVideoAddImageLoader()
	});

	videoHeaderEl.addEventListener('autostartNotAllowed', () => {
	    removeVideoAddImageHeader()
	});

	const autoPlayForCanPlay = function ()  { this.play() }
	videoLoaderEl.addEventListener('canplay', autoPlayForCanPlay)
	videoHeaderEl.addEventListener('canplay', autoPlayForCanPlay)
});
