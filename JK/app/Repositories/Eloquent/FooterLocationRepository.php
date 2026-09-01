<?php

namespace App\Repositories\Eloquent;

use App\Models\FooterLocation;
use App\Repositories\Contracts\FooterLocationRepositoryInterface;

class FooterLocationRepository extends BaseRepository implements FooterLocationRepositoryInterface
{
    public function __construct(FooterLocation $model)
    {
        parent::__construct($model);
    }
}
