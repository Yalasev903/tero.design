@extends('layouts.app')

@section('content')
<div class="contact">
    <div id="js-map" style="width: 100%; height: 500px;"></div>

    {{-- Блок с контактами --}}
    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px; margin-top: 20px;">
        {{-- Левая колонка --}}
        <div class="contact-left" style="flex: 1;">
            <div class="contact-top">
                <div class="contact-location">{{ $footer_left ?? 'POLAND, Kraków' }}</div>
                <div class="contact-time">
                    Current date:
                    <span id="js-time" data-time="{{ now()->timestamp }}">
                        {{ now()->format('F j, H:i:s') }}
                    </span>
                </div>
            </div>
            <ul class="contact-list">
                @if(!empty($contact_data['email']))
                    <li><a href="mailto:{{ $contact_data['email'] }}">{{ $contact_data['email'] }}</a></li>
                @endif
            </ul>
        </div>

        {{-- Правая колонка --}}
        <div class="contact-left" style="flex: 1;">
            <div class="contact-top">
                <div class="contact-location">{{ $footer_right ?? 'SWITZERLAND, Geneva' }}</div>
                @if(!empty($footer_right_note))
                    <div class="contact-time">{{ $footer_right_note }}</div>
                @endif
            </div>
            <ul class="contact-list">
                {{-- можно добавить телефон или соцсети --}}
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Подключение Google Maps --}}
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_qC_DS2JJQgtWKRH21DDJ7l6Uqpq6aNo&callback=googleMapInit">
</script>

<script>
    function googleMapInit() {
        const mapCenter = { lat: {{ $map_data['lat'] ?? 50.4501 }}, lng: {{ $map_data['lng'] ?? 30.5234 }} };

        const map = new google.maps.Map(document.getElementById("js-map"), {
            center: mapCenter,
            zoom: {{ $map_data['zoom'] ?? 14 }},
            disableDefaultUI: true,
            scrollwheel: false,
            styles: {!! json_encode([
                ["featureType" => "all", "elementType" => "labels.text.fill", "stylers" => [["saturation" => 36], ["color" => "#000000"], ["lightness" => 40]]],
                ["featureType" => "all", "elementType" => "labels.text.stroke", "stylers" => [["visibility" => "on"], ["color" => "#000000"], ["lightness" => 16]]],
                ["featureType" => "all", "elementType" => "labels.icon", "stylers" => [["visibility" => "off"]]],
                ["featureType" => "administrative", "elementType" => "geometry.fill", "stylers" => [["color" => "#000000"], ["lightness" => 20]]],
                ["featureType" => "administrative", "elementType" => "geometry.stroke", "stylers" => [["color" => "#000000"], ["lightness" => 17], ["weight" => 1.2]]],
                ["featureType" => "landscape", "elementType" => "geometry", "stylers" => [["color" => "#000000"], ["lightness" => 20]]],
                ["featureType" => "poi", "elementType" => "geometry", "stylers" => [["color" => "#000000"], ["lightness" => 21]]],
                ["featureType" => "road.highway", "elementType" => "geometry.fill", "stylers" => [["color" => "#000000"], ["lightness" => 17]]],
                ["featureType" => "road.highway", "elementType" => "geometry.stroke", "stylers" => [["color" => "#000000"], ["lightness" => 29], ["weight" => 0.2]]],
                ["featureType" => "road.arterial", "elementType" => "geometry", "stylers" => [["color" => "#000000"], ["lightness" => 18]]],
                ["featureType" => "road.local", "elementType" => "geometry", "stylers" => [["color" => "#000000"], ["lightness" => 16]]],
                ["featureType" => "transit", "elementType" => "geometry", "stylers" => [["color" => "#000000"], ["lightness" => 19]]],
                ["featureType" => "water", "elementType" => "geometry", "stylers" => [["color" => "#000000"], ["lightness" => 17]]]
            ]) !!}
        });

        new google.maps.Marker({
            position: mapCenter,
            map: map,
            icon: '{{ asset("multimedia/" . ($map_data["marker"]["link"] ?? "marker.png")) }}'
        });

        // Поведение при взаимодействии
        map.addListener('click', () => map.setOptions({ scrollwheel: true }));
        map.addListener('drag', () => map.setOptions({ scrollwheel: true }));
        map.addListener('mouseout', () => map.setOptions({ scrollwheel: false }));
    }

    // Обновление времени
    document.addEventListener('DOMContentLoaded', () => {
        const timeEl = document.getElementById('js-time');
        if (timeEl) {
            setInterval(() => {
                const now = new Date();
                timeEl.textContent = now.toLocaleString('en-US', {
                    month: 'long',
                    day: 'numeric'
                }) + ', ' + now.toLocaleTimeString('en-GB');
            }, 1000);
        }
    });
</script>
@endsection
