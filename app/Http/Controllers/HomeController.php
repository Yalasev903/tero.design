<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // если используешь Query Builder или Eloquent

class HomeController extends Controller
{
    public function index()
    {
        $projects_grid_raw = DB::table('home_projects_grid')
        ->leftJoin('projects', 'home_projects_grid.project_id', '=', 'projects.id')
        ->select(
            'home_projects_grid.*',
            'projects.title as project_title',
            'projects.text2 as project_text2'
        )
        ->orderBy('row_number')
        ->orderBy('col_number')
        ->get();

        $projects_grid = [];
        foreach ($projects_grid_raw as $item) {
            $projects_grid[$item->row_number][$item->col_number] = [
                'project_id' => $item->project_id,
                'media' => $item->media,
                'is_mobile' => (bool)$item->is_mobile,
                'title' => $item->project_title ?? '',
                'text2' => $item->project_text2 ?? '',
            ];
        }

        // Сортировка
        ksort($projects_grid);
        foreach ($projects_grid as &$row) {
            ksort($row);
            $row = array_values($row);
        }
        unset($row);

        return view('home', compact('projects_grid'));
    }

}
