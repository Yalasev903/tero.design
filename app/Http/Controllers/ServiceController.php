<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        // Сортируем по position, потом по id (если понадобится)
        $service_list = Service::orderBy('position')->get();

        return view('services', compact('service_list'));
    }
}
