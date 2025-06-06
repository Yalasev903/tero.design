<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowreelController extends Controller
{
    public function update(Request $request)
    {
        $type = $request->input('type'); // 'img' или 'video'
        $file = $request->input('file'); // путь файла
        $mime = $request->input('mime') ?? null;
        $width = $request->input('width') ?? 1920;
        $height = $request->input('height') ?? 1080;

        $media = $type === 'video'
            ? [
                'type' => 'video',
                'links' => [[ 'link' => $file, 'mime' => $mime ?? 'video/webm' ]],
                'width' => $width,
                'height' => $height
            ]
            : [
                'type' => 'img',
                'link' => $file,
                'width' => $width,
                'height' => $height
            ];

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
}
