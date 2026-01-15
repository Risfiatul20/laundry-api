<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaundryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function summary()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        return response()->json([
            'success' => true,
            'data' => [
                'total_customers' => User::where('role', 'pelanggan')->count(),
                'total_transactions' => LaundryTransaction::count(),
                'pending_transactions' => LaundryTransaction::where('payment_status', 'pending')->count(),
                'today_transactions' => LaundryTransaction::whereDate('created_at', $today)->count(),
                'today_revenue' => LaundryTransaction::whereDate('created_at', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total_price'),
                'month_revenue' => LaundryTransaction::where('created_at', 'like', "{$thisMonth}%")
                    ->where('payment_status', 'paid')
                    ->sum('total_price'),
                'total_revenue' => LaundryTransaction::where('payment_status', 'paid')
                    ->sum('total_price'),
            ]
        ]);
    }

    public function transactions(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $transactions = LaundryTransaction::with(['customer', 'service', 'status', 'paymentMethod'])
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_transactions' => $transactions->count(),
            'total_weight' => $transactions->sum('weight_kg'),
            'total_revenue' => $transactions->where('payment_status', 'paid')->sum('total_price'),
            'pending_payment' => $transactions->where('payment_status', 'pending')->sum('total_price'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'transactions' => $transactions
            ]
        ]);
    }

    public function revenue(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'nullable|in:day,month',
        ]);

        $groupBy = $request->group_by ?? 'day';
        $dateFormat = $groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';

        $revenue = LaundryTransaction::select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(weight_kg) as total_weight'),
                DB::raw("SUM(CASE WHEN payment_status = 'paid' THEN total_price ELSE 0 END) as revenue"),
                DB::raw("SUM(CASE WHEN payment_status = 'pending' THEN total_price ELSE 0 END) as pending")
            )
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $revenue
        ]);
    }
}
