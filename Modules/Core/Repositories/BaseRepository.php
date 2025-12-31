<?php

namespace Modules\Core\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Modules\Core\Services\CacheManager\CacheManager;
use Modules\Core\Traits\Filterable;

abstract class BaseRepository implements BaseRepositoryInterface
{
    use Filterable;

    public $model;
    public ?string $modelName;
    public ?string $modelKey;
    protected string $tableName;

    protected int $perPage = 25;
    protected bool $isCached = false;
    protected int $cacheTTl = 60; // 60 min

    protected CacheManager $cacheManager;

    /**
     * Initalize class properties
     *
     * @return void
     */
    public function __construct()
    {
        $this->modelName = $this->modelName ?? class_basename($this->model);
        $this->tableName = $this->model->getTable();
        $this->modelKey = $this->modelKey ?? $this->tableName;
        $this->boot();
    }

    public function boot(): void
    {
        $this->cacheManager = resolve(CacheManager::class, [
            "ttl" => $this->cacheTTl * 60,
            "isEnable" => $this->isCached,
            "model" => $this->model,
        ]);
    }

    /**
     * Get all data from database
     *
     * @param  Request $request
     * @param  array $with
     *
     * @return object
     */
    public function fetchAll(array $filterable = [], array $with = []): object
    {
        $this->validateFiltering($filterable);
        $rows = $this->model::query();

        $fetched = $this->cacheManager->make(
            relates: $with,
            callback: function () use ($rows, $filterable, $with) {
                return $this->getFiltered($rows, $filterable, $with);
            },
            identifier: [$filterable, $with]
        );

        return $fetched;
    }

    /**
     * Get object or redirect to 404.
     *
     * @param  mixed $id
     * @param  mixed $with
     *
     * @return object
     */
    public function fetch(int|string $id, array $with = []): object
    {
        $rows = $this->model::query();
        if ($with != []) {
            $rows = $rows->with($with);
        }

        $fetched = $this->cacheManager->make(
            relates: $with,
            callback: function () use ($rows, $id) {
                return $rows->findOrFail($id);
            },
            identifier: [$id, $with]
        );

        return $fetched;
    }

    /**
     * query multiple "and where" queries and return elequent object.
     *
     * @depends This method will be removed in next version.
     *
     * @param  array $conditions
     * @param  array $with
     *
     * @return object
     */
    public function queryFetch(array $conditions, array $with = []): object
    {

        $rows = $this->model::query()->where($conditions);

        if ($with != []) {
            $rows = $rows->with($with);
        }

        return $rows;
    }

    /**
     * Update or Create
     *
     * @param  array $match
     * @param  array $data
     *
     * @return object
     */
    public function updateOrStore(array $match, array $data): object
    {

        $updated = $this->model->updateOrCreate($match, $data);

        $this->cacheManager->flushAllCache();

        return $updated;
    }

    /**
     * fetchOrStore
     *
     * @param  array $data
     *
     * @return object
     */
    public function fetchOrStore(array $data): object
    {

        $created = $this->model->firstOrCreate($data);

        $this->cacheManager->flushAllCache();

        return $created;
    }

    /**
     * store
     *
     * @param  array $data
     *
     * @return object
     */
    public function store(array $data): object
    {
        $created = $this->model->create($data)->fresh();

        $this->cacheManager->flushAllCache();

        return $created;
    }

    /**
     * Saves data with current model instance
     *
     * @param  object $model
     * @param  array $data
     *
     * @return object
     */
    public function save(object $model, array $data): object
    {

        $model->fill($data)->save();

        $this->cacheManager->flushAllCache();

        return $model;
    }

    /**
     * Query database with id and update.
     *
     * @param  array $data
     * @param  string|int $id
     *
     * @return object
     */
    public function update(array $data, string|int $id): object
    {

        $updated = $this->model->whereId($id)->firstOrFail();
        $updated->update($data);

        $this->cacheManager->flushAllCache();

        return $updated;
    }

    /**
     * Bulk insert data.
     *
     * @param  array $data
     *
     * @return bool
     */
    public function insert(array $data): bool
    {

        $insert = DB::table($this->tableName)->insert($data);

        $this->cacheManager->flushAllCache();

        return $insert;
    }

    /**
     * Query database with id and delete.
     *
     * @param  int|string $id
     *
     * @return object
     */
    public function delete(int|string $id): object
    {

        $deleted = $this->fetch($id);
        $deleted->delete();


        $this->cacheManager->flushAllCache();

        return $deleted;
    }

    /**
     * Query database with ids and delete. Returns count for deleted ids
     *
     * @param  int|string $ids
     *
     * @return int
     */
    public function bulkDelete(array $ids): int
    {

        $deleteCount = $this->model::destroy($ids);

        $this->cacheManager->flushAllCache();

        return $deleteCount;
    }

    /**
     * Query Database with id and force delete.
     *
     * @param int|string $id
     *
     * @return object
     */
    public function forceDelete(int|string $id): object
    {

        $deleted = $this->model->onlyTrashed()->findOrFail($id);
        $deleted->forceDelete();


        $this->cacheManager->flushAllCache();

        return $deleted;
    }

    /**
     * Query database with id and restore.
     *
     * @param  int|string $id
     *
     * @return object
     */
    public function restore(int|string $id): object
    {

        $deleted = $this->model->onlyTrashed()->findOrFail($id);
        $deleted->restore();

        $this->cacheManager->flushAllCache();

        return $deleted;
    }

    /**
     * Query database with ids and bulk restore. Returns count of restored query.
     *
     * @param  array $id
     *
     * @return int
     */
    public function bulkRestore(array $ids): int
    {

        $restoreCount = $this->model::withTrashed()
            ->whereIn("id", $ids)
            ->restore();

        $this->cacheManager->flushAllCache();

        return $restoreCount;
    }

    /**
     * Sync relations
     *
     * It dose not requires any event
     *
     * @param object $model
     * @param string $relation
     * @param mixed $attributes
     * @param boolean $detaching
     *
     * @return object
     */
    public function sync(
        object $model,
        string $relation,
        mixed $attributes,
        bool $detaching = true
    ): object {
        $model->{$relation}()->sync($attributes, $detaching);
        $this->cacheManager->flushAllCache();
        return $model;
    }

    /**
     * Sync relation without detaching.
     *
     * @param object $model
     * @param string $relation
     * @param array $attributes
     *
     * @return object
     */
    public function syncWithoutDetaching(object $model, string $relation, array $attributes): object
    {
        $this->sync($model, $relation, $attributes, false);
        return $model;
    }

    /**
     * Fetch by specific column
     *
     * @param string $column
     * @param mixed $value
     * @param array $with
     * @param boolean $multiple
     * @return mixed
     */
    public function getBy(
        string $column,
        mixed $value,
        array $with = [],
        bool $multiple = false
    ): mixed {

        $rows = $this->model::query();
        if ($with != []) {
            $rows = $rows->with($with);
        }
        $fetched = $this->cacheManager->make(
            relates: $with,
            callback: function () use ($rows, $column, $value, $multiple) {
                if (is_array($value)) {
                    $rows->whereIn($column, $value);
                } else {
                    $rows->where($column, $value);
                }
                if ($multiple) {
                    return $rows->get();
                } else {
                    return $rows->first();
                }
            },
            identifier: [$column, $value, $with, $multiple]
        );

        return $fetched;
    }

    /**
     * Query database with condition and update multiple rows.
     *
     * @param array $conditions
     * @param array $data
     * @return int
     */
    public function bulkUpdate(array $conditions, array $data): int
    {

        $updated = $this->model->where($conditions)->update($data);

        $this->cacheManager->flushAllCache();

        return $updated;
    }
}