<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'customer_id',
        'cashier_id',
        'service_id',
        'status_id',
        'payment_method_id',
        'weight_kg',
        'price_per_kg',
        'total_price',
        'notes',
        'payment_status',
        'received_at',
        'estimated_completion_at',
        'completed_at',
        'picked_up_at',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
            'price_per_kg' => 'decimal:2',
            'total_price' => 'decimal:2',
            'received_at' => 'datetime',
            'estimated_completion_at' => 'datetime',
            'completed_at' => 'datetime',
            'picked_up_at' => 'datetime',
        ];
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function service()
    {
        return $this->belongsTo(LaundryService::class, 'service_id');
    }

    public function status()
    {
        return $this->belongsTo(LaundryStatus::class, 'status_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    // Generate unique transaction code
    public static function generateCode(): string
    {
        $prefix = 'LDR';
        $date = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}{$date}{$random}";
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
