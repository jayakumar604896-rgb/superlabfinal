<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function allWithTrashed()
    {
        return $this->model->withTrashed()->get();
    }

    public function onlyTrashed()
    {
        return $this->model->onlyTrashed()->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWithTrashed($id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->find($id);
        $record->update($attributes);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        return $record->delete();
    }

    public function restore($id)
    {
        $record = $this->findWithTrashed($id);
        return $record->restore();
    }

    public function forceDelete($id)
    {
        $record = $this->findWithTrashed($id);
        return $record->forceDelete();
    }
}
