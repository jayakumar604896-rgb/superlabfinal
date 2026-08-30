<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'original_price',
        'image',
        'status',
        'home_collection_available',
        'popular',
        'fasting_condition',
        'test_components',
        'faqs',
    ];

    protected $casts = [
        'home_collection_available' => 'boolean',
        'popular' => 'boolean',
        'test_components' => 'array',
        'faqs' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
