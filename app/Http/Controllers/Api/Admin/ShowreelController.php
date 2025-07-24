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
                $vimeoLink = $request->input('vimeo_link');

                if (!$vimeoLink || !is_string($vimeoLink)) {
                    return response()->json(['error' => 'Некорректная ссылка Vimeo'], 422);
                }

                Showreel::updateOrCreate(
                    ['id' => 1],
                    [
                        'poster' => null, // больше не нужен
                        'video' => $vimeoLink,
                        'media' => json_encode([
                            'type' => 'vimeo',
                            'link' => $vimeoLink
                        ], JSON_UNESCAPED_UNICODE)
                    ]
                );

                return response()->json(['success' => true]);
            } catch (\Throwable $e) {
                Log::error('Ошибка при сохранении Showreel', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                ]);

                return response()->json(['error' => 'Ошибка сервера: ' . $e->getMessage()], 500);
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

