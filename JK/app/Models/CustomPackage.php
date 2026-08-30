<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name',
        'items',
        'subtotal_amount',
        'discount_percentage',
        'discount_amount',
        'total_price',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal_amount' => 'integer',
        'discount_percentage' => 'integer',
        'discount_amount' => 'integer',
        'total_price' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
