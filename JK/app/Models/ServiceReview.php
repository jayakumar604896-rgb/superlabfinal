<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'package_id',
        'customer_id',
        'reviewer_name',
        'rating',
        'message',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function itemLabel(): string
    {
        if ($this->service) {
            return $this->service->title;
        }

        if ($this->package) {
            return $this->package->name;
        }

        return 'Unknown item';
    }
}
