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

            $project = Project::create($data);

            $folderName = null;
            $existingMedia = collect($data['multimedia_grid'] ?? [])->flatten(1);

            foreach ($existingMedia as $item) {
                $link = $item['link'] ?? ($item['poster'] ?? null);
                if ($link && str_starts_with($link, 'multimedia/')) {
                    $parts = explode('/', $link);
                    if (isset($parts[1])) {
                        $folderName = $parts[1];
                        break;
                    }
                }
                if (isset($item['images'])) {
                    foreach ($item['images'] as $img) {
                        if (str_starts_with($img, 'multimedia/')) {
                            $parts = explode('/', $img);
                            if (isset($parts[1])) {
                                $folderName = $parts[1];
                                break 2;
                            }
                        }
                    }
                }
                if (isset($item['links'])) {
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

            if (!$folderName) {
                $folderName = Str::slug($project->title) . '-' . $project->id;
            }

            $folderPath = "multimedia/$folderName";

            if (!Storage::disk('multimedia')->exists($folderName)) {
                if (!Storage::disk('multimedia')->makeDirectory($folderName)) {
                    throw new \Exception("Ошибка при создании папки '$folderPath'");
                }
            }

            $movedFiles = [];

            if (!empty($data['multimedia_grid'])) {
                foreach ($data['multimedia_grid'] as $rowIndex => $row) {
                    foreach ($row as $colIndex => $col) {
                        $data['multimedia_grid'][$rowIndex][$colIndex] = $this->moveMediaToFolder($col, $folderPath, $movedFiles);
                    }
                }

                $project->update([
                    'multimedia_grid' => $data['multimedia_grid'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Проект '{$project->title}' успешно создан",
                'project' => array_merge($project->toArray(), ['folder' => $folderName]),
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

            $project->update($data);

            $existingMedia = collect($project->multimedia_grid)->flatten(1);
            $folderName = null;

            foreach ($existingMedia as $item) {
                $link = $item['link'] ?? ($item['poster'] ?? null);
                if ($link && str_starts_with($link, 'multimedia/')) {
                    $parts = explode('/', $link);
                    if (isset($parts[1])) {
                        $folderName = $parts[1];
                        break;
                    }
                }
                if (isset($item['images'])) {
                    foreach ($item['images'] as $img) {
                        if (str_starts_with($img, 'multimedia/')) {
                            $parts = explode('/', $img);
                            if (isset($parts[1])) {
                                $folderName = $parts[1];
                                break 2;
                            }
                        }
                    }
                }
                if (isset($item['links'])) {
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

            if (!$folderName) {
                $folderName = Str::slug($project->title) . '-' . $project->id;
            }

            $folderPath = "multimedia/$folderName";

            if (!Storage::disk('multimedia')->exists($folderName)) {
                if (!Storage::disk('multimedia')->makeDirectory($folderName)) {
                    throw new \Exception("Ошибка при создании папки '$folderPath'");
                }
            }

            $movedFiles = [];

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

                $project->update([
                    'multimedia_grid' => $data['multimedia_grid'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Проект '{$project->title}' успешно обновлён",
                'project' => array_merge($project->toArray(), ['folder' => $folderName]),
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

private function moveMediaToFolder(array $col, string $folderPath, array &$movedFiles = []): array
{
    $move = function ($path) use ($folderPath, &$movedFiles) {
        $path = ltrim($path, '/');

        // Если файл уже находится в нужной папке — возвращаем как есть
        if (Str::startsWith($path, $folderPath)) return $path;

        $oldPath = public_path("multimedia/$path");
        $filename = basename($path);
        $newPath = "$folderPath/$filename";
        $newFullPath = public_path($newPath);

        // Если файл уже лежит в public и существует — не трогаем
        if (!file_exists($oldPath)) {
            // Если новый путь уже существует — не нужно двигать
            if (file_exists($newFullPath)) {
                return $newPath;
            }
            // Во всех остальных случаях — оставляем путь как есть
            return $path;
        }

        // Если файл уже находится на новом месте — возвращаем
        if ($oldPath === $newFullPath || file_exists($newFullPath)) {
            return $newPath;
        }

        // Перемещаем
        if (!File::move($oldPath, $newFullPath)) {
            throw new \Exception("Ошибка при перемещении: $oldPath → $newFullPath");
        }

        $movedFiles[] = "$oldPath → $newPath";
        return $newPath;
    };

    switch ($col['type']) {
        case 'img':
            if (!empty($col['link'])) {
                $col['link'] = $move($col['link']);
            }
            break;

        case 'video':
            if (!empty($col['poster'])) {
                $col['poster'] = $move($col['poster']);
            }
            if (!empty($col['links']) && is_array($col['links'])) {
                foreach ($col['links'] as &$link) {
                    if (!empty($link['link'])) {
                        $link['link'] = $move($link['link']);
                    }
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
        return response()->json(array_merge($project->toArray(), [
            // опционально добавим folder, если нужно
        ]));
    }

    public function index()
    {
        $projects = Project::orderBy('id')->get();
        return response()->json($projects);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Проект удалён']);
    }
}
