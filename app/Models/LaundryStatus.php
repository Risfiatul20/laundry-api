<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sequence',
        'color',
        'description',
    ];

    public function transactions()
    {
        return $this->hasMany(LaundryTransaction::class, 'status_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence');
    }
}
