<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function all();

    public function allWithTrashed();

    public function onlyTrashed();

    public function find($id);

    public function findWithTrashed($id);

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);

    public function restore($id);

    public function forceDelete($id);
}
