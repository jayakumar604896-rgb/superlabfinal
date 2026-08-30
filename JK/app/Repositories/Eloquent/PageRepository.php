<?php

namespace App\Repositories\Eloquent;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }
}
