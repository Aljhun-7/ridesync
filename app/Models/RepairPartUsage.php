<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairPartUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_booking_id',
        'spare_part_id',
        'mechanic_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class, 'service_booking_id');
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }
}
