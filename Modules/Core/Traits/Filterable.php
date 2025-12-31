<?php

namespace Modules\Core\Traits;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;

trait Filterable
{
    protected int $perPage = 25;
    protected bool $getTrashed = false;
    public bool $getOnlyTrashed = false;
    private array $comparisonOperators = [
        "__gt_" => ">",
        "__gte_" => ">=",
        "__lt_" => "<",
        "__lte_" => "<=",
        "__eq_" => "=",
        "__neq_" => "!=",
        "__in_" => "IN",
        "__nin_" => "NOT IN",
        "__like_" => "LIKE",
        "__nlike_" => "NOT LIKE",
        "__null_" => "IS NULL",
        "__nnull_" => "IS NOT NULL",
    ];

    private array $searchComparisons = [
        "LIKE",
        "NOT LIKE",
    ];

    /**
     *
     * Validates filterable parameters.
     *
     * @param array $filterable
     *
     * @return array
     */
    public function validateFiltering(array $filterable): array
    {
        try {
            $rules = [
                "per_page" => "sometimes|numeric",
                "page" => "sometimes|numeric",
                "no_paginate" => "sometimes|boolean",
                "sort_by" => "sometimes",
                "sort_order" => "sometimes|in:asc,desc",
                "search" => "sometimes|string|min:2",
                "filter_by" => "sometimes|string",
                "filter" => "sometimes",
            ];
            $messages = [
                "per_page.numeric" => "Per page count must be a number.",
                "page.numeric" => "Page must be a number.",
                "sort_order.in" => "Order must be 'asc' or 'desc'.",
                "search.string" => "Search query must be a string.",
                "search.min" => "Search query must be at least 2 character.",
                "filter_by.string" => "Filter by must be a string.",
            ];
            $validator = Validator::make(
                data: $filterable,
                rules: $rules,
                messages: $messages
            );

            $data = $validator->validated();
        } catch (Exception $exception) {
            throw $exception;
        }

        return $data;
    }

    /**
     * Get filtered data.
     *
     * @param  object $rows
     * @param  array $filterable
     * @param  array $with
     *
     * @return object
     */
    public function getFiltered(object $rows, array $filterable, array $with = []): object
    {
        try {
            $filterable = new Fluent($filterable);

            $this->loadTrashed($rows);
            $this->loadOnlyTrashed($rows, $filterable);
            $this->loadRelationships($rows, $with);
            $this->loadSearch($rows, $filterable);
            $this->loadFiltered($rows, $filterable);
            $this->loadSorted($rows, $filterable);
            $this->loadCompared($rows, $filterable);

            $resources = $this->loadPaginated($rows, $filterable);
        } catch (Exception $exception) {
            throw $exception;
        }

        return $resources;
    }

    /**
     * Loads soft deleted data.
     *
     * @param  object $rows
     *
     * @return object
     */
    protected function loadTrashed(object $rows): object
    {
        try {
            if ($this->getTrashed) {
                $rows = $rows->withTrashed();
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $rows;
    }

    /**
     * Query only from trashed data.
     *
     * @param  mixed $rows
     * @return object
     */
    protected function loadOnlyTrashed(object $rows, object $filterable): object
    {
        try {
            if (
                $this->getOnlyTrashed
                || $filterable->get_only_trashed
            ) {
                $rows = $rows->onlyTrashed();
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $rows;
    }

    /**
     * loadRelationships loads relationship.
     *
     * @param  mixed $rows
     * @param  mixed $with
     *
     * @return object
     */
    protected function loadRelationships(object $rows, array $with): object
    {
        try {
            if ($with != []) {
                $rows = $rows->with($with);
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $rows;
    }

    /**
     * Search by filterable params in current instance of eloquent.
     *
     * @param  object $rows
     * @param  object $filterable
     *
     * @return object
     */
    protected function loadSearch(object $rows, object $filterable): object
    {
        try {
            if ($filterable->search) {
                $rows = $rows->whereLike($this->model::SEARCHABLE, "%{$filterable->search}%");
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $rows;
    }

    /**
     * Filter by filterable params in current instance of eloquent.
     *
     * @param  object $rows
     * @param  object $filterable
     *
     * @return object
     */
    protected function loadFiltered(object $rows, object $filterable): object
    {
        try {
            if (
                $filterable->filter_by
                && $filterable->filter
                && in_array($filterable->filter_by, $this->model::SEARCHABLE)
            ) {
                $rows = $rows->whereLike($filterable->filter_by, $filterable->filter);
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $rows;
    }

    /**
     * Sort by filterable params in current instance of eloquent
     *
     * @param  object $rows
     * @param  object $filterable
     *
     * @return object
     */
    protected function loadSorted(object $rows, object $filterable): object
    {
        try {
            $sortBy = $filterable->sort_by ?? "created_at";
            $rows = $filterable->sort_order == "asc"
                ? $rows->oldest($sortBy)
                : $rows->latest($sortBy);
        } catch (Exception $exception) {
            throw $exception;
        }

        return $rows;
    }

    /**
     * Compare filterable params in current instance of eloquent
     *
     * @param  object $rows
     * @param  object $filterable
     *
     * @return object
     */
    protected function loadCompared(object $rows, object $filterable): object
    {
        try {
            $parameters = array_keys($filterable->toArray());
            $comparisonParameters = array_filter($parameters, function ($parameter) {
                preg_match("/^__[a-zA-Z]+_/", $parameter, $match);
                return count($match) > 0;
            });
            foreach ($comparisonParameters as $comparisonParameter) {
                preg_match("/^__[a-zA-Z]+_/", $comparisonParameter, $comparison);
                $comparison = end($comparison);
                $parameter = str_replace($comparison, "", $comparisonParameter);
                $compareWith = $filterable->{$comparisonParameter};
                if (!in_array($parameter, $this->model::SEARCHABLE)) {
                    continue;
                }
                if (in_array($this->comparisonOperators[$comparison], $this->searchComparisons)) {
                    $compareWith = "%{$compareWith}%";
                }
                if ($this->comparisonOperators[$comparison] == "IN") {
                    $rows = $rows->whereIn($parameter, $compareWith);
                } elseif ($this->comparisonOperators[$comparison] == "NOT IN") {
                    $rows = $rows->whereNotIn($parameter, $compareWith);
                } else {
                    $rows = $rows->where(
                        $parameter,
                        $this->comparisonOperators[$comparison],
                        $compareWith
                    );
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $rows;
    }

    /**
     * Paginate or get all data.
     *
     * @param  object $rows
     * @param  object $filterable
     *
     * @return object
     */
    protected function loadPaginated(object $rows, object $filterable): object
    {
        try {
            $perPage = (int) ($filterable->per_page ?? $this->perPage);
            $paginate = (bool) $filterable->no_paginate;
            $resources = !$paginate
                ? $rows->paginate($perPage)->appends(request()->except("page"))
                : $rows->get();
        } catch (Exception $exception) {
            throw $exception;
        }

        return $resources;
    }
}