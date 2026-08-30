<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }

    public function log($action, $description)
    {
        return $this->model->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }
}
