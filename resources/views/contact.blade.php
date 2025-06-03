@extends('layouts.app')

@section('content')
    <div class="contact">
        <div class="contact-map" id="js-map"></div>
        <div class="contact-left" style="display:inline-block">
            <div class="contact-top">
                <div class="contact-location">POLAND, Kraków</div>
                <div class="contact-time">Current date:
                    <span id="js-time" data-time="{{ now()->timestamp }}">
                        {{ now()->format('F j, H:i:s') }}
                    </span>
                </div>
            </div>
            <ul class="contact-list">
                <li><a href="mailto:{{ $base_config['email'] }}">{{ $base_config['email'] }}</a></li>
                {{-- <li><a href="tel:{{ preg_replace('/\s+/', '', $base_config['phoneNumber']) }}">{{ $base_config['phoneNumber'] }}</a></li> --}}
            </ul>
        </div>
        <div class="contact-left" style="display:inline-block">
            <div class="contact-top">
                <div class="contact-location">SWITZERLAND, Geneve</div>
                <div class="contact-time">OAKS GROUP SA, Associate Partner</div>
            </div>
            <ul class="contact-list">
                <li><a href="mailto:3d@ewm.swiss">3d@ewm.swiss</a></li>
                <li><a href="tel:+41227003794">+41 22 700 37 94</a></li>
            </ul>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- GOOGLE MAP --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCCu2s5XjqkNw97bLeZflhZoZZycZlQbJk&callback=googleMapInit" async defer></script>
    <script>
        function googleMapInit() {
            let mapLng = new google.maps.LatLng({{ $map_data['lat'] }}, {{ $map_data['lng'] }});
            let mapOptions = {
                zoom: {{ $map_data['zoom'] }},
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
                center: mapLng,
                styles: [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]
            };
            let mapElement = document.getElementById('js-map');
            let map = new google.maps.Map(mapElement, mapOptions);
            new google.maps.Marker({
                position: mapLng,
                map: map,
                icon: '/multimedia/{{ $map_data['marker']['link'] }}'
            });
            map.addListener('click', function() {
                map.setOptions({ scrollwheel: true });
            });
            map.addListener('drag', function() {
                map.setOptions({ scrollwheel: true });
            });
            map.addListener('mouseout', function() {
                map.setOptions({ scrollwheel: false });
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            let timeEl = document.getElementById('js-time');
            if (timeEl) {
                setInterval(function () {
                    let date = new Date();
                    timeEl.textContent = date.toLocaleString('en-US', { month: 'long', day: 'numeric' }) + ', ' +
                        date.toLocaleTimeString('en-GB');
                }, 1000);
            }
        });
    </script>
@endsection

