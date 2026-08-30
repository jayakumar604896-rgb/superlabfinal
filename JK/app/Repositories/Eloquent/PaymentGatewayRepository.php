<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentGateway;
use App\Repositories\Contracts\PaymentGatewayRepositoryInterface;

class PaymentGatewayRepository extends BaseRepository implements PaymentGatewayRepositoryInterface
{
    public function __construct(PaymentGateway $model)
    {
        parent::__construct($model);
    }
}
