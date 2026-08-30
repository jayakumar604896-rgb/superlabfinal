<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactEnquiry;
use App\Repositories\Contracts\ContactEnquiryRepositoryInterface;

class ContactEnquiryRepository extends BaseRepository implements ContactEnquiryRepositoryInterface
{
    public function __construct(ContactEnquiry $model)
    {
        parent::__construct($model);
    }
}
