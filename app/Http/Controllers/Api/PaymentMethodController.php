<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentMethod::query();

        if (!$request->user() || !$request->user()->isAdmin()) {
            $query->active();
        }

        $methods = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $methods
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $method = PaymentMethod::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil dibuat',
            'data' => $method
        ], 201);
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json([
            'success' => true,
            'data' => $paymentMethod
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $paymentMethod->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil diupdate',
            'data' => $paymentMethod->fresh()
        ]);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil dihapus'
        ]);
    }
}
