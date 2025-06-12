<?php

namespace App\Http\Controllers;

use App\Models\TblService;

class ServiceController extends Controller
{
    public function index()
    {
        $service_list = TblService::orderBy('position')->get();
        return view('services', compact('service_list'));
    }
}
