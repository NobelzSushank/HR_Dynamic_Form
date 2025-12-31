<?php

namespace Modules\Core\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

trait ResourceRelationships
{
    public function relationshipResource(string $field, string $resource): JsonResource|MissingValue
    {
        return $resource::make($this->whenLoaded($field));
    }

    public function relationshipCollection(string $field, string $resource): JsonResource|MissingValue
    {
        return $resource::collection($this->whenLoaded($field));
    }
}