<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

        $project = Project::create($data);
        $safeName = Str::slug($project->title) . '-' . $project->id;
        $folderPath = "multimedia/$safeName";

        // 🟡 Создание папки и проверка
        $created = Storage::disk('multimedia')->makeDirectory($safeName);
        if (!$created) {
            $project->delete();
            return response()->json([
                'message' => "Не удалось создать папку '$folderPath'"
            ], 500);
        }

        // 🟡 Перемещение медиа с проверкой
        $movedAll = true;
        $errors = [];

        if (!empty($data['multimedia_grid'])) {
            foreach ($data['multimedia_grid'] as $rowIndex => $row) {
                foreach ($row as $colIndex => $col) {
                    try {
                        $data['multimedia_grid'][$rowIndex][$colIndex] = $this->moveMediaToFolder($col, $folderPath);
                    } catch (\Throwable $e) {
                        $movedAll = false;
                        $errors[] = "Ошибка перемещения файла в строке $rowIndex колонке $colIndex: " . $e->getMessage();
                    }
                }
            }

            $project->update([
                'multimedia_grid' => $data['multimedia_grid'],
            ]);
        }

        if (!$movedAll) {
            return response()->json([
                'message' => 'Проект создан, но возникли ошибки при перемещении файлов.',
                'project' => $project,
                'folder' => $folderPath,
                'errors' => $errors
            ], 207); // 207 Multi-Status (частично выполнено)
        }

        return response()->json([
            'message' => "Проект '{$project->title}' успешно создан",
            'project' => $project,
            'folder' => $folderPath
        ]);
    }

    private function moveMediaToFolder(array $col, string $folderPath): array
    {
        $move = function ($path) use ($folderPath) {
            $oldPath = public_path("multimedia/$path");
            $filename = basename($path);
            $newPath = "$folderPath/$filename";
            $newFullPath = public_path("multimedia/$newPath");

            if (!file_exists($oldPath)) {
                throw new \Exception("Файл не найден: $oldPath");
            }

            if (!File::move($oldPath, $newFullPath)) {
                throw new \Exception("Не удалось переместить $oldPath → $newFullPath");
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
