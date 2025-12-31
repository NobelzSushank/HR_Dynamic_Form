<?php

namespace Modules\User\Transformers;

use Modules\Core\Transformers\BaseResource;

class RoleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return $this->convert($request, [
            'id' => $this->uuid,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ], false);
    }
}
