<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'quantity',
        'reorder_level',
        'unit_cost',
        'price',
        'supplier',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reorder_level' => 'integer',
            'unit_cost' => 'decimal:2',
            'price' => 'decimal:2',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }
}
