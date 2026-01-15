<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaundryService;
use App\Models\LaundryStatus;
use App\Models\LaundryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = LaundryTransaction::with(['customer', 'cashier', 'service', 'status', 'paymentMethod']);

        // Filter berdasarkan role
        if ($user->isPelanggan()) {
            $query->where('customer_id', $user->id);
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by transaction code or customer name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:laundry_services,id',
            'weight_kg' => 'required|numeric|min:0.1',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $service = LaundryService::findOrFail($request->service_id);
            $firstStatus = LaundryStatus::ordered()->first();

            $receivedAt = now();
            $estimatedCompletion = $receivedAt->copy()->addHours($service->estimated_hours);

            $transaction = LaundryTransaction::create([
                'transaction_code' => LaundryTransaction::generateCode(),
                'customer_id' => $request->customer_id,
                'cashier_id' => $request->user()->isKasir() || $request->user()->isAdmin() 
                    ? $request->user()->id 
                    : null,
                'service_id' => $service->id,
                'status_id' => $firstStatus->id,
                'weight_kg' => $request->weight_kg,
                'price_per_kg' => $service->price_per_kg,
                'total_price' => $request->weight_kg * $service->price_per_kg,
                'notes' => $request->notes,
                'received_at' => $receivedAt,
                'estimated_completion_at' => $estimatedCompletion,
            ]);

            DB::commit();

            $transaction->load(['customer', 'cashier', 'service', 'status']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaction
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(LaundryTransaction $transaction)
    {
        $transaction->load(['customer', 'cashier', 'service', 'status', 'paymentMethod']);

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    public function update(Request $request, LaundryTransaction $transaction)
    {
        $request->validate([
            'service_id' => 'sometimes|exists:laundry_services,id',
            'weight_kg' => 'sometimes|numeric|min:0.1',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['notes']);

            if ($request->has('service_id') || $request->has('weight_kg')) {
                $service = $request->has('service_id') 
                    ? LaundryService::findOrFail($request->service_id)
                    : $transaction->service;
                
                $weight = $request->weight_kg ?? $transaction->weight_kg;

                $data['service_id'] = $service->id;
                $data['weight_kg'] = $weight;
                $data['price_per_kg'] = $service->price_per_kg;
                $data['total_price'] = $weight * $service->price_per_kg;
                $data['estimated_completion_at'] = $transaction->received_at
                    ->copy()
                    ->addHours($service->estimated_hours);
            }

            $transaction->update($data);

            DB::commit();

            $transaction->load(['customer', 'cashier', 'service', 'status', 'paymentMethod']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate',
                'data' => $transaction
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, LaundryTransaction $transaction)
    {
        $request->validate([
            'status_id' => 'required|exists:laundry_statuses,id',
        ]);

        $status = LaundryStatus::findOrFail($request->status_id);

        $data = ['status_id' => $status->id];

        // Set completed_at jika status "Selesai"
        if ($status->sequence === 4 && !$transaction->completed_at) {
            $data['completed_at'] = now();
        }

        // Set picked_up_at jika status "Diambil"
        if ($status->sequence === 5 && !$transaction->picked_up_at) {
            $data['picked_up_at'] = now();
        }

        $transaction->update($data);
        $transaction->load(['customer', 'cashier', 'service', 'status', 'paymentMethod']);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate',
            'data' => $transaction
        ]);
    }

    public function processPayment(Request $request, LaundryTransaction $transaction)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        if ($transaction->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah dibayar'
            ], 400);
        }

        $transaction->update([
            'payment_method_id' => $request->payment_method_id,
            'payment_status' => 'paid',
            'cashier_id' => $transaction->cashier_id ?? $request->user()->id,
        ]);

        $transaction->load(['customer', 'cashier', 'service', 'status', 'paymentMethod']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diproses',
            'data' => $transaction
        ]);
    }

    public function track($code)
    {
        $transaction = LaundryTransaction::with(['service', 'status'])
            ->where('transaction_code', $code)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_code' => $transaction->transaction_code,
                'service' => $transaction->service->name,
                'status' => $transaction->status,
                'weight_kg' => $transaction->weight_kg,
                'total_price' => $transaction->total_price,
                'payment_status' => $transaction->payment_status,
                'received_at' => $transaction->received_at,
                'estimated_completion_at' => $transaction->estimated_completion_at,
                'completed_at' => $transaction->completed_at,
            ]
        ]);
    }
}
