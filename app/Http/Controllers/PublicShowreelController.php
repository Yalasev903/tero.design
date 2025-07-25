<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Showreel;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PublicShowreelController extends Controller
{
   public function __invoke(): View
    {
        $row = Showreel::first();
        $media = $row ? json_decode($row->media, true) : null;
        $vimeoLink = $media['type'] === 'vimeo' ? $media['link'] : null;

        return view('components.showreel', [
            'vimeoLink' => $vimeoLink
        ]);
    }
}
