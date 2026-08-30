<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'starts_at',
        'expires_at',
        'usage_limit',
        'usage_count',
        'guest_eligible',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'guest_eligible' => 'boolean',
    ];

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'coupon_customer')->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function setCodeAttribute(?string $value): void
    {
        $this->attributes['code'] = strtoupper(trim((string) $value));
    }

    public function hasCustomerAssignments(): bool
    {
        return $this->customers()->exists();
    }
}
