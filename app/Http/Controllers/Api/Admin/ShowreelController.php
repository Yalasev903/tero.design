<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowreelController extends Controller
{
    public function update(Request $request)
    {
        $media = $request->input('media');

        if (!is_array($media)) {
            return response()->json(['error' => 'Invalid media payload'], 422);
        }

        DB::table('home_projects_grid')->updateOrInsert(
            ['row_number' => 0, 'col_number' => 0],
            [
                'project_id' => 0,
                'media' => json_encode($media),
                'is_mobile' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
    public function show()
    {
        $row = DB::table('home_projects_grid')
            ->where('row_number', 0)
            ->where('col_number', 0)
            ->first();

        if (!$row) {
            return response()->json(['media' => null]);
        }

        $media = json_decode($row->media, true);
        return response()->json(['media' => $media]);
    }
}
