<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerVital extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'metric_name',
        'metric_value',
        'unit',
        'normal_range',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
