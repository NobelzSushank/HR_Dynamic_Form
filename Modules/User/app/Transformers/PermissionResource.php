<?php

namespace Modules\User\Transformers;

use Modules\Core\Transformers\BaseResource;

class PermissionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return $this->convert($request, [
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ], false);
    }
}
