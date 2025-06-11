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
            // Всегда инициализируем строку как массив!
            if (!isset($grid[$item->row_number]) || !is_array($grid[$item->row_number])) {
                $grid[$item->row_number] = [];
            }
            $grid[$item->row_number][$item->col_number] = [
                'project_id' => $item->project_id,
                'media' => $mediaArr,
                'is_mobile' => $item->is_mobile ?? false,
                'title' => $item->project_title,
                'text2' => $item->project_text2,
            ];
        }

        // Теперь каждая строка гарантированно массив
        // Преобразуем в массив массивов (без дыр)
        $gridOut = [];
        ksort($grid);
        foreach ($grid as $row) {
            if (is_array($row)) {
                // пересортируем колонки, чтобы не было дыр
                ksort($row);
                $gridOut[] = array_values($row);
            }
        }

        return response()->json($gridOut);
    }

    public function update(Request $request)
    {
        try {
            $grid = $request->input('grid', []);
            \Log::info('HomeGridController@update — входные данные:', ['grid' => $grid]);
            \DB::table('home_projects_grid')->truncate();

            foreach ($grid as $rowIdx => $row) {
                foreach ($row as $colIdx => $col) {
                    $media = $col['media'] ?? [];
                    if (is_string($media)) {
                        $mediaDecoded = json_decode($media, true);
                        $media = $mediaDecoded !== null ? $mediaDecoded : $media;
                    }
                    \DB::table('home_projects_grid')->insert([
                        'row_number'   => $rowIdx,
                        'col_number'   => $colIdx,
                        'project_id'   => $col['project_id'] ?? null,
                        'media'        => json_encode($media, JSON_UNESCAPED_UNICODE),
                        'is_mobile'    => $col['is_mobile'] ?? false,
                    ]);
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
