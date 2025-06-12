<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblService;

class TblServiceController extends Controller
{
    public function index()
    {
        return TblService::orderBy('position')->get([
            'col_id',
            'col_title',
            'col_description',
            'col_video',
            'position',
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'col_title' => 'required|string|max:255',
            'col_description' => 'required|string',
            'col_video' => 'nullable|string',
        ]);

        $service = TblService::create($data);

        return response()->json($service, 201);
    }

    public function show($id)
    {
        $service = TblService::findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = TblService::findOrFail($id);

        $data = $request->validate([
            'col_title' => 'required|string|max:255',
            'col_description' => 'required|string',
            'col_video' => 'nullable|string',
        ]);

        $service->update($data);

        return response()->json($service);
    }

    public function reorder(Request $request)
    {
        $services = $request->input('services');
        if (!is_array($services)) {
            return response()->json(['error' => 'Invalid data'], 422);
        }

        foreach ($services as $item) {
            if (isset($item['col_id'], $item['position'])) {
                TblService::where('col_id', $item['col_id'])->update(['position' => $item['position']]);
            }
        }

        return response()->json(['message' => 'Позиции обновлены']);
    }


    public function destroy($id)
    {
        $service = TblService::findOrFail($id);
        $service->delete();

        return response()->noContent();
    }
}
