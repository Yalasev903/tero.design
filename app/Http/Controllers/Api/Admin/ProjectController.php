<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:266',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
            'meta_title' => 'nullable|string|max:266',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords' => 'nullable|string|max:260',
            'multimedia_grid' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // 1. Создаём проект в БД
            $project = Project::create($data);

            // 2. Генерируем имя папки
            $folderName = Str::slug($project->title) . '-' . $project->id;
            $folderPath = "multimedia/$folderName";

            $created = Storage::disk('multimedia')->makeDirectory($folderName);
            if (!$created) {
                throw new \Exception("Ошибка при создании папки '$folderPath'");
}

            // 4. Перемещаем все медиа
            if (!empty($data['multimedia_grid'])) {
                foreach ($data['multimedia_grid'] as $rowIndex => $row) {
                    foreach ($row as $colIndex => $col) {
                        $data['multimedia_grid'][$rowIndex][$colIndex] = $this->moveMediaToFolder($col, $folderPath);
                    }
                }

                // 5. Обновляем JSON в проекте
                $project->update([
                    'multimedia_grid' => $data['multimedia_grid'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Проект '{$project->title}' успешно создан",
                'project' => $project,
                'folder' => $folderPath,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Ошибка создания проекта: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ошибка создания проекта',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    private function moveMediaToFolder(array $col, string $folderPath): array
    {
        $move = function ($path) use ($folderPath) {
            $oldPath = public_path("multimedia/" . ltrim($path, '/'));
            $filename = basename($path);
            $newPath = "$folderPath/$filename";
            $newFullPath = public_path($newPath);

            if (!file_exists($oldPath)) {
                throw new \Exception("Файл не найден: $oldPath");
            }

            if (!File::move($oldPath, $newFullPath)) {
                throw new \Exception("Ошибка при перемещении: $oldPath → $newFullPath");
            }

            return $newPath;
        };

        switch ($col['type']) {
            case 'img':
                $col['link'] = $move($col['link']);
                break;
            case 'video':
                if (!empty($col['poster'])) {
                    $col['poster'] = $move($col['poster']);
                }
                if (!empty($col['links']) && is_array($col['links'])) {
                    foreach ($col['links'] as &$link) {
                        $link['link'] = $move($link['link']);
                    }
                }
                break;
            case 'curtain':
                if (!empty($col['images']) && is_array($col['images'])) {
                    foreach ($col['images'] as &$img) {
                        $img = $move($img);
                    }
                }
                break;
        }

        return $col;
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    public function index()
    {
        $projects = Project::orderBy('id')->get();
        return response()->json($projects);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:266',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
            'meta_title' => 'nullable|string|max:266',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords' => 'nullable|string|max:260',
            'multimedia_grid' => 'nullable|array',
        ]);

        $project->update($data);

        return response()->json($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Проект удалён']);
    }
}
