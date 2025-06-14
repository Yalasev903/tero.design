<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
    {
        return DB::table('tbl_faq')->orderBy('position', 'asc')->get();
    }

    public function destroy($id)
    {
        DB::table('tbl_faq')->where('col_id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function updateAll(Request $request)
    {
        foreach ($request->all() as $index => $item) {
            DB::table('tbl_faq')->updateOrInsert(
                ['col_id' => $item['col_id'] ?? null],
                [
                    'col_question' => $item['col_question'] ?? '',
                    'col_answer' => $item['col_answer'] ?? '',
                    'position' => $index,
                    'updated_at' => now()
                ]
            );
        }

        return response()->json(['success' => true]);
    }

        public function store(Request $request)
    {
        $id = DB::table('tbl_faq')->insertGetId([
            'col_question' => $request->col_question,
            'col_answer'   => $request->col_answer,
            'position'     => $request->position ?? 0,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'col_id' => $id,
            'col_question' => $request->col_question,
            'col_answer' => $request->col_answer,
            'position' => $request->position ?? 0
        ]);
    }
}
