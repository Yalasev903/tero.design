<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Showreel;

class ProjectController extends Controller
{
    public function show($id)
    {
        $project = \App\Models\Project::findOrFail($id);
        $vimeoLink = optional(json_decode(optional(Showreel::first())->media, true))['link'] ?? null;

         // SEO
        $seo = [
            'title' => $project->meta_title ?? $project->title ?? 'Tero Project',
            'description' => $project->meta_description ?? '',
            'keywords' => $project->meta_keywords ?? '',
        ];

        return view('project', compact('project', 'vimeoLink', 'seo'));
    }
}
