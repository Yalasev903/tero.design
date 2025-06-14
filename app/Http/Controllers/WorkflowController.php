<?php

namespace App\Http\Controllers;

use App\Models\TblWorkflow;
use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflow = TblWorkflow::first();
        $faq_list = \App\Models\Faq::orderBy('col_id')->get();

         $seo = DB::table('tbl_pages')
            ->select([
                'col_meta_title as title',
                'col_meta_description as description',
                'col_meta_keywords as keywords'
            ])
            ->where('col_title', 'workflow')
            ->first();


        return view('workflow', compact('workflow', 'faq_list', 'seo'));
    }
}
