<?php 
namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface 
{
    public function all(array $columns = ['*'], array $relation = []): Collection;

    public function allTrashed(): Collection;

    public function findById(int $modelId, array $columns = ['*'], array $relation = [], array $append = []): ?Model;

    public function findTrashedById(int $modelId): ?Model;

    public function create (array $payload): ?Model;

    public function update(int $modelId, array $payload): bool;

    public function deleteById(int $modelId): bool;

    public function restoreById(int $modelId): bool;

    public function permanentlyDeleteById(int $modelId): bool;

    public function inactiveById(int $modelId): bool;

    public function activeById(int $modelId): bool;
}

