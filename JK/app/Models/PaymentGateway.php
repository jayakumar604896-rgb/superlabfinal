<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'api_key',
        'api_secret',
        'webhook_secret',
        'environment',
        'status',
        'additional_settings',
    ];

    protected $casts = [
        'additional_settings' => 'array',
    ];
}
