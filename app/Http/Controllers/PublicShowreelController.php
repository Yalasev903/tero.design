<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Showreel;
use Illuminate\Http\JsonResponse;

class PublicShowreelController extends Controller
{
        public function __invoke(): JsonResponse
    {
        $row = Showreel::first();

        $media = $row ? json_decode($row->media, true) : null;

        return response()->json(['media' => $media]);
    }
}
