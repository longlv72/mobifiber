<?php

namespace App\Repository\Eloquent;

use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface 
{
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $relation = []): Collection
    {
        return $this->model->with($relation)->get($columns);
    }

    public function allTrashed(): Collection 
    {
        return $this->model->onlyTrashed()->get();
    }

    public function findById(int $modelId, array $columns = ['*'], array $relation = [], array $append = []): ?Model 
    {
        return $this->model->select($columns)->with($relation)->findOrFail($modelId)->append($append);
    }

    public function findTrashedById(int $modelId): ?Model
    {
        return $this->model->withTrashed()->findOrFail($modelId);
    }

    public function findOnlyTrashedById(int $modelId): ?Model
    {
        return $this->model->onlyTrashed()->findOrFail($modelId);
    }

    public function create (array $payload): ?Model
    {
        $model = $this->model->create($payload);
        return $model->refresh();
    }

    public function update(int $modelId, array $payload): bool
    {
        $model = $this->findById($modelId);
        return $model->update($payload);
    }

    public function deleteById(int $modelId): bool
    {
        return $this->findById($modelId)->delete();
    }

    public function restoreById(int $modelId): bool
    {
        return $this->findOnlyTrashedById($modelId)->restore();
    }

    public function permanentlyDeleteById(int $modelId): bool
    {
        return $this->findOnlyTrashedById($modelId)->restore();
    }

    public function inactiveById(int $modelId): bool
    {
        $model = $this->findById($modelId);
        return $model->update(['is_active' => 0]);
    }

    public function activeById(int $modelId): bool
    {
        $model = $this->findById($modelId);
        return $model->update(['is_active' => 1]);
    }

}
