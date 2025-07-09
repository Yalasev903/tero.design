<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeGridController extends Controller
{
    public function index()
    {
        $rows = DB::table('home_projects_grid')
            ->leftJoin('projects', 'home_projects_grid.project_id', '=', 'projects.id')
            ->select(
                'home_projects_grid.*',
                'projects.title as project_title',
                'projects.text2 as project_text2'
            )
            ->orderBy('row_number')
            ->orderBy('col_number')
            ->get();

        $grid = [];

        foreach ($rows as $item) {
            $media = $item->media ?? '';
            $mediaArr = is_array($media) ? $media : (json_decode($media, true) ?: []);

            $grid[] = [
                'row_number' => $item->row_number,
                'col_number' => $item->col_number,
                'project_id' => $item->project_id,
                'media' => $mediaArr,
                'is_mobile' => $item->is_mobile ?? false,
                'has_link' => $item->has_link ?? true,
                'title' => $item->project_title,
                'text2' => $item->project_text2,
            ];
        }

        return response()->json($grid);
    }

    public function update(Request $request)
    {
        try {
            $grid = $request->input('grid', []);

            if (!is_array($grid) || count($grid) === 0) {
                return response()->json(['error' => 'Нет данных для сохранения'], 400);
            }

            $keysToKeep = [];

            foreach ($grid as $col) {
                if (empty($col['project_id']) && empty($col['media'])) continue;

                $row = $col['row_number'] ?? 0;
                $colNum = $col['col_number'] ?? 0;

                $media = $col['media'] ?? [];
                if (is_string($media)) {
                    $decoded = json_decode($media, true);
                    $media = is_array($decoded) ? $decoded : [];
                }

                DB::table('home_projects_grid')->updateOrInsert(
                    ['row_number' => $row, 'col_number' => $colNum],
                    [
                        'project_id' => $col['project_id'] ?? null,
                        'media' => json_encode($media, JSON_UNESCAPED_UNICODE),
                        'is_mobile' => $col['is_mobile'] ?? false,
                        'has_link' => $col['has_link'] ?? true,
                        'updated_at' => now(),
                    ]
                );

                $keysToKeep[] = ['row_number' => $row, 'col_number' => $colNum];
            }

            // 💥 Вместо whereNotIn — мы удалим по списку исключений вручную
            if (!empty($keysToKeep)) {
                $existing = DB::table('home_projects_grid')->get(['row_number', 'col_number']);

                foreach ($existing as $cell) {
                    $found = collect($keysToKeep)->contains(function ($item) use ($cell) {
                        return $item['row_number'] == $cell->row_number && $item['col_number'] == $cell->col_number;
                    });

                    if (!$found) {
                        DB::table('home_projects_grid')
                            ->where('row_number', $cell->row_number)
                            ->where('col_number', $cell->col_number)
                            ->delete();
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            \Log::error('Ошибка при сохранении HomeGrid', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['error' => 'Ошибка сервера: ' . $e->getMessage()], 500);
        }
    }
}
