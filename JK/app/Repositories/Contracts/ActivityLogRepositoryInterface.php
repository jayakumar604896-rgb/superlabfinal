<?php

namespace App\Repositories\Contracts;

interface ActivityLogRepositoryInterface extends BaseRepositoryInterface
{
    public function log($action, $description);
}
