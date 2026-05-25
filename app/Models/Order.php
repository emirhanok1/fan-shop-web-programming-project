<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'balance_used',
        'card_amount',
        'status',
        'shipping_address',
        'invoice_no',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'total_amount' => 'decimal:2',
            'balance_used' => 'decimal:2',
            'card_amount' => 'decimal:2',
            'status' => 'string',
        ];
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Get the user that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the tracking record for the order.
     */
    public function tracking(): HasOne
    {
        return $this->hasOne(OrderTracking::class);
    }

    /**
     * Get the transactions associated with the order.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
