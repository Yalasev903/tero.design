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

            // Создаём проект
            $project = Project::create($data);

            // Проверяем — может файлы уже есть в старой папке?
            $folderName = null;
            $existingMedia = collect($data['multimedia_grid'] ?? [])->flatten(1);

            foreach ($existingMedia as $item) {
                $link = $item['link'] ?? ($item['poster'] ?? null);
                if ($link && is_string($link) && str_starts_with($link, 'multimedia/')) {
                    $parts = explode('/', $link);
                    if (isset($parts[1])) {
                        $folderName = $parts[1];
                        break;
                    }
                }

                if (isset($item['images']) && is_array($item['images'])) {
                    foreach ($item['images'] as $img) {
                        if (is_string($img) && str_starts_with($img, 'multimedia/')) {
                            $parts = explode('/', $img);
                            if (isset($parts[1])) {
                                $folderName = $parts[1];
                                break 2;
                            }
                        }
                    }
                }

                if (isset($item['links']) && is_array($item['links'])) {
                    foreach ($item['links'] as $video) {
                        if (!empty($video['link']) && str_starts_with($video['link'], 'multimedia/')) {
                            $parts = explode('/', $video['link']);
                            if (isset($parts[1])) {
                                $folderName = $parts[1];
                                break 2;
                            }
                        }
                    }
                }
            }

            // Если нет — генерим новую
            if (!$folderName) {
                $folderName = Str::slug($project->title) . '-' . $project->id;
            }

            $folderPath = "multimedia/$folderName";

            // Создаём, если нет
            if (!Storage::disk('multimedia')->exists($folderName)) {
                $created = Storage::disk('multimedia')->makeDirectory($folderName);
                if (!$created) throw new \Exception("Ошибка при создании папки '$folderPath'");
            }

            // Перемещаем медиа
            $movedFiles = [];
            if (!empty($data['multimedia_grid'])) {
                foreach ($data['multimedia_grid'] as $rowIndex => $row) {
                    foreach ($row as $colIndex => $col) {
                        $data['multimedia_grid'][$rowIndex][$colIndex] = $this->moveMediaToFolder($col, $folderPath, $movedFiles);
                    }
                }

                // Обновляем в проекте
                $project->update([
                    'multimedia_grid' => $data['multimedia_grid'],
                ]);
            }

            DB::commit();

            $project->folder = $folderName; 

            return response()->json([
                'message' => "Проект '{$project->title}' успешно создан",
                'project' => $project,
                'folder' => $folderPath,
                'moved_files' => $movedFiles
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

    private function moveMediaToFolder(array $col, string $folderPath, array &$movedFiles = []): array
    {
        $move = function ($path) use ($folderPath, &$movedFiles) {
            $path = ltrim($path, '/');
            if (Str::startsWith($path, $folderPath)) return $path;

            $oldPath = public_path("multimedia/$path");
            $filename = basename($path);
            $newPath = "$folderPath/$filename";
            $newFullPath = public_path($newPath);

            if (!file_exists($oldPath)) {
                throw new \Exception("Файл не найден: $oldPath");
            }

            // Проверка: если уже перемещён
            if ($oldPath === $newFullPath || file_exists($newFullPath)) {
                return $newPath;
            }

            if (!File::move($oldPath, $newFullPath)) {
                throw new \Exception("Ошибка при перемещении: $oldPath → $newFullPath");
            }

            $movedFiles[] = "$oldPath → $newPath";
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

        try {
            DB::beginTransaction();

            // Обновляем поля проекта
            $project->update($data);

            // Попытка найти уже существующую подпапку
            $existingMedia = collect($project->multimedia_grid)->flatten(1);
            $folderName = null;

            foreach ($existingMedia as $item) {
                $link = $item['link'] ?? ($item['poster'] ?? null);

                if ($link && is_string($link) && str_starts_with($link, 'multimedia/')) {
                    $parts = explode('/', $link);
                    if (isset($parts[1])) {
                        $folderName = $parts[1]; // multimedia/this_folder/file.ext
                        break;
                    }
                }

                // Для шторки (curtain)
                if (isset($item['images']) && is_array($item['images'])) {
                    foreach ($item['images'] as $img) {
                        if (is_string($img) && str_starts_with($img, 'multimedia/')) {
                            $parts = explode('/', $img);
                            if (isset($parts[1])) {
                                $folderName = $parts[1];
                                break 2;
                            }
                        }
                    }
                }

                // Для видео (links)
                if (isset($item['links']) && is_array($item['links'])) {
                    foreach ($item['links'] as $video) {
                        if (!empty($video['link']) && str_starts_with($video['link'], 'multimedia/')) {
                            $parts = explode('/', $video['link']);
                            if (isset($parts[1])) {
                                $folderName = $parts[1];
                                break 2;
                            }
                        }
                    }
                }
            }

            // Если не нашли — создаём по-новому
            if (!$folderName) {
                $folderName = Str::slug($project->title) . '-' . $project->id;
            }

            $folderPath = "multimedia/$folderName";

            // Создаём папку если её нет
            if (!Storage::disk('multimedia')->exists($folderName)) {
                $created = Storage::disk('multimedia')->makeDirectory($folderName);
                if (!$created) throw new \Exception("Ошибка при создании папки '$folderPath'");
            }

            $movedFiles = [];

            // Перемещаем медиафайлы
            if (!empty($data['multimedia_grid'])) {
                foreach ($data['multimedia_grid'] as $rowIndex => $row) {
                    foreach ($row as $colIndex => $col) {
                        try {
                            $data['multimedia_grid'][$rowIndex][$colIndex] = $this->moveMediaToFolder($col, $folderPath, $movedFiles);
                        } catch (\Throwable $e) {
                            Log::warning('Ошибка перемещения файла: ' . $e->getMessage());
                        }
                    }
                }

                // Обновляем мультимедиа
                $project->update([
                    'multimedia_grid' => $data['multimedia_grid'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Проект '{$project->title}' успешно обновлён",
                'project' => $project,
                'folder' => $folderPath,
                'moved_files' => $movedFiles
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Ошибка обновления проекта: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ошибка обновления проекта',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Проект удалён']);
    }
}
