<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'current_price',
        'previous_price',
        'average_price',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_price' => 'decimal:2',
            'previous_price' => 'decimal:2',
            'average_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
