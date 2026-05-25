<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTracking extends Model
{
    use HasFactory;

    // Use only updated_at timestamp since there is no created_at column
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $table = 'order_tracking';

    protected $fillable = [
        'order_id',
        'step',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'step' => 'string',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Check if the tracking step is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->step === 'delivered';
    }

    /**
     * Return the next logical step in the tracking flow.
     * Flow: sourcing -> packaging -> shipped -> on_the_way -> delivered
     */
    public function nextStep(): ?string
    {
        $steps = ['sourcing', 'packaging', 'shipped', 'on_the_way', 'delivered'];
        $currentIndex = array_search($this->step, $steps);
        
        if ($currentIndex !== false && $currentIndex < count($steps) - 1) {
            return $steps[$currentIndex + 1];
        }
        
        return null;
    }

    /**
     * Get the order that this tracking belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
