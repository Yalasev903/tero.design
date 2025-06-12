<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        // Отдаём все услуги с нужными полями без alias, чтобы Vue получил поля как есть
        return Service::orderBy('position')->get(['id', 'title', 'description', 'video', 'position']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'nullable', // может быть строкой или json
            'position' => 'integer|min:0',
        ]);

        $service = Service::create($data);
        return response()->json($service, 201);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'nullable',
            'position' => 'integer|min:0',
        ]);

        $service->update($data);
        return response()->json($service);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return response()->noContent();
    }

    /**
     * Сохранение порядка услуг
     * POST /api/admin/services/reorder
     * payload: { services: [{id: 1, position: 1}, ...] }
     */
    public function reorder(Request $request)
    {
        $services = $request->input('services');
        if (!is_array($services)) {
            return response()->json(['error' => 'Invalid data'], 422);
        }

        foreach ($services as $item) {
            if (isset($item['id'], $item['position'])) {
                Service::where('id', $item['id'])->update(['position' => $item['position']]);
            }
        }

        return response()->json(['message' => 'Позиции обновлены']);
    }
}
