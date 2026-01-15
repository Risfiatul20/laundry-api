<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaundryService;
use Illuminate\Http\Request;

class LaundryServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = LaundryService::query();

        // Non-admin hanya lihat yang aktif
        if (!$request->user() || !$request->user()->isAdmin()) {
            $query->active();
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $services = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_per_kg' => 'required|numeric|min:0',
            'estimated_hours' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $service = LaundryService::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dibuat',
            'data' => $service
        ], 201);
    }

    public function show(LaundryService $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    public function update(Request $request, LaundryService $service)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'price_per_kg' => 'sometimes|numeric|min:0',
            'estimated_hours' => 'sometimes|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil diupdate',
            'data' => $service->fresh()
        ]);
    }

    public function destroy(LaundryService $service)
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dihapus'
        ]);
    }
}
