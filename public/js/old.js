// создадим элемент с прокруткой
let div = document.createElement('div');

div.style.overflowY = 'scroll';
div.style.width = '50px';
div.style.height = '50px';

// мы должны вставить элемент в документ, иначе размеры будут равны 0
document.body.appendChild(div);
let scrollWidth = div.offsetWidth - div.clientWidth;
div.parentNode.removeChild(div);


$(function() {
    'use strict';


    $('.b-lazy').lazyLoadXT();


    /* Grid
    ------------------------------------------------------- */
    let $win_width = $(window).outerWidth(); //ширина окна
    let $videoCount = $('#js-gallery').find('video').length;


    if ($win_width > 1024) {
        var $interval = setInterval(mediaLoaded, 200);
    } else {
        $('.grid-item').each(function(){
            let $item = $(this);

            if ($item.hasClass('grid-item-desktop')) {
                $item.remove();
            }


            if ($item.hasClass('grid-item-curtain')) {
                //let $row_height = $row.height();
                //let $item_width = $item.width();
                let $curtain = $item.find('.curtain'); //шторка
                let $curtain_orientation = ($curtain.hasClass('curtain-v')) ? 'vertical' : 'horizontal'; //движение шторки ("horizontal" или "vertical")

                //$item.width($item_width);
                //$curtain.height($row_height);
                $curtain.twentytwenty({
                    before_label: '',
                    after_label: '',
                    orientation: $curtain_orientation
                });
            }
        });

        $('#page').imagesLoaded(function(){
            $('#page').removeClass('loading');
        });
    }

    function mediaLoaded() {
        if ($videoLoaded >= $videoCount) {
            clearInterval($interval);

            $('#page').imagesLoaded(function(){
                $('.grid-row').each(function(){
                    calculateCoeff($(this));
                });

                $('#page').removeClass('loading');
            });
        }
    }


    function calculateCoeff($row) {
        let $items_width = 0;
        let $row_width = $(window).outerWidth() - scrollWidth;
        let $row_height = $row.height();
        let $coeff = 0;

        $row.find('.grid-item').each(function(){
            let $item = $(this);
            $items_width += $item.width();
        });

        $coeff = $row_width / $items_width;
        $row.height($row_height * $coeff);
        iframeSizes($row, $coeff);
    }


    function iframeSizes($row, $coeff) {
        $row.find('.grid-item').each(function(){
            let $item = $(this);
            //let $item_width = $item.width();

            //$item.width($item_width * $coeff);
            $item.addClass('load');

            if (! $item.hasClass('grid-item-360') && ! $item.hasClass('grid-item-curtain')) {
                let $media_width = $item.find('.js-grid-item-media').width();
                $item.width($media_width);
                console.log($media_width);
            }


            if ($item.hasClass('grid-item-360')) {
                $item.addClass('iframe');
            }

            if ($item.hasClass('grid-item-curtain')) {
                let $row_height = $row.height();
                let $item_width = $item.width();
                let $curtain = $item.find('.curtain'); //шторка
                let $curtain_orientation = ($curtain.hasClass('curtain-v')) ? 'vertical' : 'horizontal'; //движение шторки ("horizontal" или "vertical")

                $item.width($item_width);
                $curtain.height($row_height);
                $curtain.twentytwenty({
                    before_label: '',
                    after_label: '',
                    orientation: $curtain_orientation
                });
            }
        });
    }


    /* Share
    ------------------------------------------------------- */
    $('#js-share-toggle').on('click', function(){
        let $this = $(this);
        $this.toggleClass('open');
        $('#js-share-popup').toggleClass('open');
        return false;
    });


    $(function(){
        $(document).on('click touchstart', function(event) {
            if ($('#js-share-popup').is(':visible')) {
                if ($(event.target).closest('#js-share-popup').length) { return; }
                $('#js-share-toggle').removeClass('open');
                $('#js-share-popup').removeClass('open');
                event.stopPropagation();
            }
        });
    });


    /* FAQ
    ------------------------------------------------------- */
    $('.js-question').on('click', function() {
        let $this = $(this);
        let $answer = $this.next('.js-answer');

        if(! $this.hasClass('active')) {
            $('.js-answer').slideUp();
            $('.js-question').removeClass('active');
        }

        $this.toggleClass('active');
        $answer.slideToggle();

        return false;
    });


    /* Current time
    ------------------------------------------------------- */
    if ($('#js-time').length) {
        setInterval(function() {
            $.post('/handlers/ajax_time.php', function(data) {
                $('#js-time').text(data);
            });
        }, 60000);
    }


    /* Showreel
    ------------------------------------------------------- */
    $('.js-showreel-open').click(function () {
        $('#js-showreel').addClass('open');
        $('#page').addClass('form-open');
        mobileMenuClose();
        return false;
    });


    $('#page').on('click', '#js-showreel-close', function () {
        showreelClose();
        return false;
    });


    $(function(){
        $(document).on('click touchstart', function(event) {
            if ($('#js-showreel').hasClass('open')) {
                if ($(event.target).closest('#js-showreel').length) { return; }
                showreelClose();
                event.stopPropagation();
            }
        });
    });


    function showreelClose() {
        let $video = $('#js-video')[0];
        $video.pause();
        $video.currentTime = 0;
        $('#js-player').removeClass('play');
        $('#js-showreel').removeClass('open');
        $('#page').removeClass('form-open');
    }


    /* Video
    ------------------------------------------------------- */
    $('#page').on('click', '#js-video-play', function () {
        $('#js-video')[0].play();
        $('#js-player').addClass('play');
        return false;
    });


    $('.js-video-play').click(function () {
        let $player = $(this).parents('.js-player');
        let $video = $player.children('.js-video');
        $video[0].play();
        $player.addClass('play');
        return false;
    });


    /* Player size
    ------------------------------------------------------- */
    if ($win_width <= 1024) {
        playerSize();
        $(window).resize(playerSize);
    }

    function playerSize() {
        let $video = $('#js-video');
        let $width = $video.outerWidth();
        let $height = $width / (16 / 9);

        $('#js-player').outerHeight($height);
        $video.outerHeight($height);
    }




    /* Menu on mobile devices
    ------------------------------------------------------- */
    $('#js-menu-trigger').click(function () {
        $(this).toggleClass('open');
        $('#js-mobile-menu').toggleClass('open');
        $('#js-header').toggleClass('fixed');
        return false;
    });


    $(function(){
        $(document).on('click touchstart', function(event) {
            if ($('#js-mobile-menu').hasClass('open')) {
                if ($(event.target).closest('#js-mobile-menu').length) { return; }
                if ($(event.target).closest('#js-menu-trigger').length) { return; }
                mobileMenuClose();
                event.stopPropagation();
            }
        });
    });


    /* Privacy Policy
    ------------------------------------------------------- */
    $('#js-privacy-policy-close').click(function () {
        $('#js-privacy-policy').slideUp();
        $.post('/handlers/ajax_privacy_policy.php');
        return false;
    });


    $('#js-gallery').lightGallery({
        getCaptionFromTitleOrAlt: false,
        appendSubHtmlTo: '.lg-item',
        selector: '.js-img',
        download: false,
        counter: false,
        prevHtml: '<svg viewBox="0 0 914 534" xmlns="http://www.w3.org/2000/svg"><path d="M914 267C914 288.025 896.931 305.126 875.944 305.126H129.932L293.443 468.938C308.273 483.796 308.273 507.998 293.443 522.856C278.612 537.714 254.454 537.714 239.623 522.856L11.0997 293.913C7.5553 290.362 4.75707 286.25 2.89157 281.578C1.02607 276.905 1.75086e-05 272.046 1.75086e-05 267.187C1.75086e-05 267.093 1.75086e-05 267.093 1.75086e-05 267C1.75086e-05 262.047 1.02605 257.095 2.98482 252.422C4.85032 247.75 7.74183 243.545 11.193 240.087L239.716 11.1435C254.547 -3.7145 278.705 -3.7145 293.536 11.1435C308.367 26.0015 308.367 50.2041 293.536 65.0621L130.025 228.874H875.944C896.931 228.874 914 245.975 914 267Z" fill="#fff"/></svg>',
        nextHtml: '<svg viewBox="0 0 914 534" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 267C0 288.025 17.0693 305.126 38.0561 305.126H784.068L620.557 468.938C605.727 483.796 605.727 507.998 620.557 522.856C635.388 537.714 659.546 537.714 674.377 522.856L902.9 293.913C906.445 290.362 909.243 286.25 911.108 281.578C912.974 276.905 914 272.046 914 267.187C914 267.093 914 267.093 914 267C914 262.047 912.974 257.095 911.015 252.422C909.15 247.75 906.258 243.545 902.807 240.087L674.284 11.1435C659.453 -3.7145 635.295 -3.7145 620.464 11.1435C605.633 26.0015 605.633 50.2041 620.464 65.0621L783.975 228.874H38.0561C17.0693 228.874 0 245.975 0 267Z" fill="#fff"/></svg>'
    });


});


/* Закрыть мобильное меню
------------------------------------------------------- */
function mobileMenuClose() {
    'use strict';
    $('#js-menu-trigger').removeClass('open');
    $('#js-mobile-menu').removeClass('open');
    $('#js-header').removeClass('fixed');
}


/* google map
	------------------------------------------------------- */
function googleMapInit() {
    let mapOptions = {
        zoom: $opt.zoom,
        zoomControl: false,
        scrollwheel: false,
        scaleControl: false,
        rotateControl: false,
        panControl: false,
        mapMaker: false,
        fullscreenControl: false,
        disableDefaultUI: false,
        streetViewControl: false,
        signInControl: false,
        mapTypeControl: false,
        center: new google.maps.LatLng($opt.lat, $opt.lng),
        styles: [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]
    };

    let mapElement = document.getElementById('js-map');
    let map = new google.maps.Map(mapElement, mapOptions);


    new google.maps.Marker({
        position: new google.maps.LatLng($opt.lat, $opt.lng),
        map: map,
        icon: '/multimedia/'+ $opt.marker
    });


    // Enable scroll zoom after click on map
    map.addListener('click', function() {
        map.setOptions({
            scrollwheel: true
        });
    });


    // Enable scroll zoom after drag on map
    map.addListener('drag', function() {
        map.setOptions({
            scrollwheel: true
        });
    });


    // Disable scroll zoom when mouse leave map
    map.addListener('mouseout', function() {
        map.setOptions({
            scrollwheel: false
        });
    });
}