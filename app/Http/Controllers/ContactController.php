<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Map;

class ContactController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $map = Map::first();
        return view('contact', [
            'contact_data' => [
                'email' => $setting->col_email ?? '',
                'phone' => $setting->col_tel ?? '',
            ],
            'map_data' => [
                'lat' => $map->col_lat ?? 0,
                'lng' => $map->col_lng ?? 0,
                'zoom' => $map->col_zoom ?? 10,
                'marker' => ['link' => $map->col_marker ?? 'default-marker.svg'],
            ],
        ]);
    }
}

