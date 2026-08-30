<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'badge',
        'discount_percentage',
        'tests_included_count',
        'original_price',
        'offer_price',
        'description',
        'test_components',
        'faqs',
        'fasting_condition',
        'status',
    ];

    protected $casts = [
        'test_components' => 'array',
        'faqs' => 'array',
    ];
}
