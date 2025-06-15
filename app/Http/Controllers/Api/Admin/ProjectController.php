<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

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

        $data['multimedia_grid'] = json_encode($data['multimedia_grid'] ?? []);

        $project = Project::create($data);

        return response()->json($project);
    }
}
