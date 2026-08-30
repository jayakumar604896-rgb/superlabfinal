<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentType;
use App\Repositories\Contracts\PaymentTypeRepositoryInterface;

class PaymentTypeRepository extends BaseRepository implements PaymentTypeRepositoryInterface
{
    public function __construct(PaymentType $model)
    {
        parent::__construct($model);
    }
}
