<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface extends BaseRepositoryInterface
{
    public function getByKey($key);
    public function updateByKey($key, $value);
}
