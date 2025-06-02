<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // если используешь Query Builder или Eloquent

class HomeController extends Controller
{
    public function index()
    {
        $projects_grid_raw = DB::table('home_projects_grid')
            ->orderBy('row_number')
            ->orderBy('col_number')
            ->get();

        $projects_grid = [];
        foreach ($projects_grid_raw as $item) {
            $projects_grid[$item->row_number][$item->col_number] = [
                'project_id' => $item->project_id,
                'media' => $item->media,
                'is_mobile' => (bool)$item->is_mobile,
                'title' => '',
                'text2' => '',
            ];
        }

        // Сортируем строки и колонки по ключу, чтобы не было дыр
        ksort($projects_grid);
        foreach ($projects_grid as &$row) {
            ksort($row);
            $row = array_values($row); // индексы 0,1,2,...
        }
        unset($row);

        return view('home', compact('projects_grid'));
    }
}
