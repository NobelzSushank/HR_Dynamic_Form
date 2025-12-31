<?php

namespace Modules\User\Transformers;

use Modules\Core\Transformers\BaseResource;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return $this->convert($request, [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->getRoleNames(),
        ], false);
    }
}
