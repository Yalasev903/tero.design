<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show($id)
    {
        $project = \App\Models\Project::findOrFail($id);
        return view('project', compact('project'));
    }
}
