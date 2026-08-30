<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'age',
        'gender',
        'blood_group',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function vitals()
    {
        return $this->hasMany(CustomerVital::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_customer')->withTimestamps();
    }
}
