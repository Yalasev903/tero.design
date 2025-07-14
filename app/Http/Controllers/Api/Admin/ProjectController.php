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
use ZipArchive;

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

            $folderName = Str::slug($project->title, '_'); // normalized
            $folderPath = "multimedia/$folderName";

            if (!Storage::disk('multimedia')->exists($folderName)) {
                Storage::disk('multimedia')->makeDirectory($folderName);
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
                    'folder' => $folderName,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Проект '{$project->title}' успешно создан",
                'project' => $project,
                'folder' => $folderPath,
                'moved_files' => $movedFiles,
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

            $folderName = Str::slug($project->title, '_'); // normalize title
            $folderPath = "multimedia/$folderName";

            if (!Storage::disk('multimedia')->exists($folderName)) {
                Storage::disk('multimedia')->makeDirectory($folderName);
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
                    'folder' => $folderName,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Проект '{$project->title}' успешно обновлён",
                'project' => $project,
                'folder' => $folderPath,
                'moved_files' => $movedFiles,
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
            $filename = basename($path);
            $newPath = "$folderPath/$filename";
            $newFullPath = public_path($newPath);
            $oldPath = public_path("multimedia/$path");

            // Пропустить если уже перемещён
            if (Str::startsWith($path, $folderPath)) return $path;

            if (!file_exists($oldPath)) {
                return file_exists($newFullPath) ? $newPath : $path;
            }

            if (!file_exists(dirname($newFullPath))) {
                File::makeDirectory(dirname($newFullPath), 0755, true, true);
            }

            if (!File::move($oldPath, $newFullPath)) {
                throw new \Exception("Ошибка при перемещении: $oldPath → $newFullPath");
            }

            $movedFiles[] = "$oldPath → $newPath";
            return $newPath;
        };

        switch ($col['type']) {
            case 'img':
                if (!empty($col['link'])) $col['link'] = $move($col['link']);
                break;

            case 'video':
                if (!empty($col['poster'])) $col['poster'] = $move($col['poster']);
                if (!empty($col['links']) && is_array($col['links'])) {
                    foreach ($col['links'] as &$link) {
                        if (!empty($link['link'])) $link['link'] = $move($link['link']);
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

    public function uploadVrTour(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $request->validate([
            'zip' => 'required|file|mimes:zip|max:304800', // макс 300 МБ
        ]);

        $zipFile = $request->file('zip');
        $projectSlug = Str::slug($project->title, '_');
        $destinationPath = public_path("360tours/$projectSlug");

        if (!File::exists(public_path('360tours'))) {
            File::makeDirectory(public_path('360tours'), 0755, true);
        }

        if (File::exists($destinationPath)) {
            File::deleteDirectory($destinationPath);
        }

        File::makeDirectory($destinationPath, 0755, true);

        $zip = new \ZipArchive;
        if ($zip->open($zipFile->getRealPath()) === true) {
            $zip->extractTo($destinationPath);
            $zip->close();
        } else {
            return response()->json([
                'message' => 'Ошибка при распаковке ZIP архива',
            ], 500);
        }

        // Рекурсивный поиск index.htm
        $indexPath = collect(File::allFiles($destinationPath))
            ->first(fn($file) => $file->getFilename() === 'index.htm');

        if (!$indexPath) {
            return response()->json([
                'message' => 'В архиве отсутствует index.htm',
            ], 422);
        }

        $relativePath = str_replace(public_path(), '', $indexPath->getPathname());
        $iframeLink = str_replace('\\', '/', $relativePath); // ← единственная правильная строка

        return response()->json([
            'message' => 'VR-тур успешно загружен',
            'iframe_src' => $iframeLink,
        ]);
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    public function index()
    {
        return response()->json(Project::orderBy('id')->get());
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Проект удалён']);
    }
}
