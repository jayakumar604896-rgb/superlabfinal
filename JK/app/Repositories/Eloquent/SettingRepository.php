<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function getByKey($key)
    {
        $setting = $this->model->where('key', $key)->first();
        return $setting ? $setting->value : null;
    }

    public function updateByKey($key, $value)
    {
        $setting = $this->model->where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
            return $setting;
        }
        return null;
    }
}
