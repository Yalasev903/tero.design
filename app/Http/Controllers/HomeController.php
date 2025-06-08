<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $batch = $request->input('batch', 0);
        $limit = 3;
        $offset = $batch * $limit;

        $showreel_row = DB::table('home_projects_grid')
            ->where('row_number', 0)
            ->first();

        $query = DB::table('home_projects_grid')
            ->where('row_number', '>', 0)
            ->leftJoin('projects', 'home_projects_grid.project_id', '=', 'projects.id')
            ->select(
                'home_projects_grid.*',
                'projects.title as project_title',
                'projects.text2 as project_text2'
            )
            ->orderBy('row_number')
            ->orderBy('col_number');

        $grid_raw = $request->ajax()
            ? $query->skip($offset)->take($limit)->get()
            : $query->get();

        $projects_grid = [];
        foreach ($grid_raw as $item) {
            $media = json_decode($item->media, true);

            $projects_grid[$item->row_number][$item->col_number] = [
                'project_id' => $item->project_id,
                'media' => $media,
                'is_mobile' => (bool)$item->is_mobile,
                'title' => $item->project_title ?? '',
                'text2' => $item->project_text2 ?? '',
            ];
        }

        ksort($projects_grid);
        foreach ($projects_grid as &$row) {
            ksort($row);
            $row = array_values($row);
        }
        unset($row);

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('components.grid-rows', compact('projects_grid'))->render()
            ]);
        }

        $settings = \App\Models\Setting::first();

        $showreel = null;
        if ($showreel_row) {
            $media = json_decode($showreel_row->media, true);
            $showreel = [
                'media' => $media,
                'width' => $media['width'] ?? null,
                'height' => $media['height'] ?? null,
                'title' => 'SHOWREEL',
            ];
        }

        return view('home', compact('projects_grid', 'settings', 'showreel'));
    }
}
