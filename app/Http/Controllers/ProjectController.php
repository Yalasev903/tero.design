<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show($id)
    {
        // Подтягивай проект из БД
        // $project = Project::findOrFail($id);
        // return view('projects.show', compact('project'));
        return "Project page for ID: $id"; // пока заглушка
    }
}
