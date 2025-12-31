<?php

namespace Modules\Core\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function fetchAll(array $filterable = [], array $with = []): object;

    public function fetch(int|string $id, array $with = []): object;

    public function queryFetch(array $conditions, array $with = []): object;

    public function updateOrStore(array $match, array $data): object;

    public function fetchOrStore(array $data): object;

    public function store(array $data): object;

    public function save(object $model, array $data): object;

    public function update(array $data, int|string $id): object;

    public function insert(array $data): bool;

    public function delete(int|string $id): object;

    public function bulkDelete(array $ids): int;

    public function forceDelete(int|string $id): object;

    public function restore(int|string $id): object;

    public function bulkRestore(array $ids): int;
}