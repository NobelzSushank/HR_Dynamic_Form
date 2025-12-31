<?php

namespace Modules\Core\Traits;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\MissingValue;

trait CustomResource
{
    /**
     * Customize transformer resource keys.
     *
     * @param  ?object|array $data
     * @param  array $fields
     *
     * @return array|MissingValue
     */
    public static function customMake(object|array $data, array $fields): array|MissingValue
    {
        $resource = $data;
        if (is_array($resource)) {
            $resource = (object) $data;
        }
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        } elseif ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        } elseif ($data instanceof MissingValue || $data == null) {
            return new MissingValue();
        }
        $data = array_merge_recursive(...array_map(function ($field) use ($resource) {
            return [
                $field => $resource->{$field}
            ];
        }, $fields));

        return $data;
    }

    /**
     * customRelationResource
     *
     * @return array|MissingValue
     */
    public function customRelationResource(
        string $field,
        string $resource,
        array $fields
    ): array|MissingValue {
        return $resource::customMake($this->whenLoaded($field), $fields);
    }
}