<?php

namespace App\Http\Controllers;

use App\Models\TblWorkflow;
use App\Models\Faq;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflow = TblWorkflow::first();
        $faq_list = Faq::orderBy('position')->get();

        return view('workflow', compact('workflow', 'faq_list'));
    }
}
