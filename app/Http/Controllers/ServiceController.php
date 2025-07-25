<?php

namespace App\Http\Controllers;

use App\Models\TblService;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index()
    {
        $service_list = TblService::orderBy('position')->get();

        // Получаем SEO для страницы services
        $seo = DB::table('tbl_pages')->where('col_title', 'services')->first();
        $vimeoLink = optional(json_decode(optional(Showreel::first())->media, true))['link'] ?? null;

        return view('services', compact('service_list', 'seo', 'vimeoLink'));
    }
}
