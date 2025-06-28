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

        Storage::disk('multimedia')->makeDirectory($safeName);

        // Перемещаем медиа
        if (!empty($data['multimedia_grid'])) {
            foreach ($data['multimedia_grid'] as $rowIndex => $row) {
                foreach ($row as $colIndex => $col) {
                    $data['multimedia_grid'][$rowIndex][$colIndex] = $this->moveMediaToFolder($col, $folderPath);
                }
            }

            $project->update([
                'multimedia_grid' => $data['multimedia_grid'],
            ]);
        }

        return response()->json([
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

            if (file_exists($oldPath)) {
                File::move($oldPath, public_path("multimedia/$newPath"));
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

    // Остальные методы без изменений

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
