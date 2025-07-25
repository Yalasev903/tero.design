<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Map;
use App\Models\Showreel;

class ContactController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $map = Map::first();
         $media = optional(json_decode(optional(Showreel::first())->media, true));
        $vimeoLink = $media->type === 'vimeo' ? $media->link : null;
        return view('contact', [
            'contact_data' => [
                'email' => $setting->col_email ?? '',
                'phone' => $setting->col_tel ?? '',
            ],
            'map_data' => [
                'lat' => $map->col_lat ?? 0,
                'lng' => $map->col_lng ?? 0,
                'zoom' => $map->col_zoom ?? 10,
                'marker' => $map->col_marker ?? 'multimedia/marker.png',
            ],
                'footer_left' => $setting->footer_left ?? '',
                'footer_right' => $setting->footer_right ?? '',
                'footer_right_note' => $setting->footer_right_note ?? '',
                'vimeoLink' => $vimeoLink,
        ]);
    }
}

