<?php

namespace App\Repositories\Eloquent;

use App\Models\Testimonial;
use App\Repositories\Contracts\TestimonialRepositoryInterface;

class TestimonialRepository extends BaseRepository implements TestimonialRepositoryInterface
{
    public function __construct(Testimonial $model)
    {
        parent::__construct($model);
    }
}
