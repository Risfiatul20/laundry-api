<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price_per_kg',
        'estimated_hours',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_kg' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(LaundryTransaction::class, 'service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
