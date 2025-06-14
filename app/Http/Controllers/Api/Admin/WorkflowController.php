<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class WorkflowController extends Controller
{
    public function show()
    {
        return DB::table('tbl_workflow')->first();
    }

    public function update(Request $request)
    {
        DB::table('tbl_workflow')->update([
            'col_description' => $request->col_description,
            'col_poster' => $request->col_poster,
            'col_video' => $request->col_video,
        ]);

        return response()->json(['success' => true]);
    }
}
