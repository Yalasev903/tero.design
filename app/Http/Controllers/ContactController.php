<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $base_config = [
            'email' => 'info@tero.design',
            // 'phoneNumber' => '+48 123 456 789', // если понадобится
            // 'EnableJivoChat' => false,         // если понадобится
            // 'JivoChatId' => '',
        ];

        $map_data = [
            'lat' => 50.054019,
            'lng' => 19.936830,
            'zoom' => 15,
            'marker' => [
                'link' => 'marker.svg', // /public/multimedia/marker.svg
            ],
        ];

        return view('contact', compact('base_config', 'map_data'));
    }
}
