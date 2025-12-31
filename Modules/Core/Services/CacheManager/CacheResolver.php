<?php

namespace Modules\Core\Services\CacheManager;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CacheResolver
{
    /**
     * Generate cache tags.
     *
     * @param string $triggeredKey
     * @param array $taggableKeys
     *
     * @return array
     */
    public function generateCacheTags(string $triggeredKey, array $taggableKeys): array
    {
        $triggeredKey = [Str::snake(Str::singular($triggeredKey))];

        $data = array_merge($triggeredKey, $taggableKeys);

        return $data;
    }

    /**
     * Resolve relational keys.
     *
     * @param array $relations
     *
     * @return array
     */
    public function resolveRelationKeys(array $relations): array
    {
        $data = [];
        foreach ($relations as $relation) {
            if ($relation instanceof Closure) {
                continue;
            }
            if (Str::contains($relation, ".")) {
                $nestedRelationKeys = explode(".", $relation);
                $tags = array_map(function ($nestedRelationKey) {
                    return Str::snake(Str::singular($nestedRelationKey));
                }, $nestedRelationKeys);
            } else {
                $tags = Str::snake(Str::singular($relation));
            }

            $data[] = $tags;
        }

        $data = Arr::flatten($data);

        return $data;
    }
}