<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalOperationalCostItem extends Model
{
    protected $table = 'internal_operational_cost_items';

    protected $fillable = [
        'internal_operational_cost_id',
        'item_name',
        'qty',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function cost(): BelongsTo
    {
        return $this->belongsTo(
            InternalOperationalCost::class,
            'internal_operational_cost_id'
        );
    }
}
