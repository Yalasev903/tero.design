<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PageSeoController extends Controller
{
    public function getHomeSeo()
    {
        $page = DB::table('tbl_pages')->where('col_title', 'index')->first();

        return response()->json([
            'seo_title' => $page->col_meta_title ?? '',
            'seo_description' => $page->col_meta_description ?? '',
            'seo_keywords' => $page->col_meta_keywords ?? '',
        ]);
    }

    public function updateHomeSeo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seo_title' => 'required|string|max:60',
            'seo_description' => 'required|string|max:300',
            'seo_keywords' => 'nullable|string|max:260',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('tbl_pages')
            ->where('col_title', 'index')
            ->update([
                'col_meta_title' => $request->seo_title,
                'col_meta_description' => $request->seo_description,
                'col_meta_keywords' => $request->seo_keywords,
            ]);

        return response()->json(['message' => 'SEO успешно обновлено']);
    }
}
