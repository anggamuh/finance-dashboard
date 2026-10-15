<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternalOperationalCost extends Model
{
    protected $fillable = [
        'transaction_date',
        'invoice_number',
        'supplier',
        'item_name',
        'qty',
        'price',
        'total',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(
            InternalOperationalCostItem::class,
            'internal_operational_cost_id'
        );
    }
}
