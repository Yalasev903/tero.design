<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show($id)
    {
        $project = \App\Models\Project::findOrFail($id);
        $vimeoLink = optional(json_decode(optional(Showreel::first())->media, true))['link'] ?? null;

        return view('project', compact('project', 'vimeoLink'));
    }
}
