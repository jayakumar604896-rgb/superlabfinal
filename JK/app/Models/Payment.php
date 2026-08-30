<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'payment_type_id',
        'transaction_id',
        'amount',
        'status',
        'payment_date',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
