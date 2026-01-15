<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaundryStatus;
use Illuminate\Http\Request;

class LaundryStatusController extends Controller
{
    public function index()
    {
        $statuses = LaundryStatus::ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sequence' => 'required|integer|min:0',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        $status = LaundryStatus::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil dibuat',
            'data' => $status
        ], 201);
    }

    public function show(LaundryStatus $status)
    {
        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }

    public function update(Request $request, LaundryStatus $status)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'sequence' => 'sometimes|integer|min:0',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        $status->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate',
            'data' => $status->fresh()
        ]);
    }

    public function destroy(LaundryStatus $status)
    {
        $status->delete();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil dihapus'
        ]);
    }
}
