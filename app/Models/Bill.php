<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'bill_number',
        'bill_date',
        'subtotal',
        'grand_total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
            'subtotal' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }
}
