<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showreel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShowreelController extends Controller
{
    public function update(Request $request)
    {
        try {
            $media = $request->input('media');

            if (!is_array($media)) {
                Log::warning('Неверный формат media', ['media' => $media]);
                return response()->json(['error' => 'Неверный формат media'], 422);
            }

            Showreel::updateOrCreate(
                ['id' => 1],
                [
                    'poster' => $media['poster'] ?? null,
                    'video' => $media['links'][0]['link'] ?? null,
                    'media' => json_encode($media, JSON_UNESCAPED_UNICODE),
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Ошибка при сохранении Showreel', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'error' => 'Ошибка сервера: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show()
    {
        try {
            $row = Showreel::first();

            $media = $row ? json_decode($row->media, true) : null;

            return response()->json(['media' => $media]);
        } catch (\Throwable $e) {
            Log::error('Ошибка при получении Showreel', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['media' => null], 500);
        }
    }
}

