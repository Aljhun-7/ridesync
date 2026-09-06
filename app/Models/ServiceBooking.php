<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceBooking extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending',
        'assigned',
        'in_progress',
        'completed',
        'cancelled',
    ];

    protected $fillable = [
        'service_code',
        'customer_id',
        'mechanic_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'vehicle',
        'service_type',
        'status',
        'scheduled_at',
        'labor_cost',
        'parts_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'labor_cost' => 'decimal:2',
            'parts_cost' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function partUsages(): HasMany
    {
        return $this->hasMany(RepairPartUsage::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->labor_cost + (float) $this->parts_cost;
    }
}
