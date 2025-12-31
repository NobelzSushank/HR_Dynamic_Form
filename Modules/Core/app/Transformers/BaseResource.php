<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Traits\CustomResource;
use Modules\Core\Traits\HasResourceAdapter;
use Modules\Core\Traits\ResourceRelationships;

class BaseResource extends JsonResource
{
    use ResourceRelationships;
    use CustomResource;
    use HasResourceAdapter;

    public function __construct($resource = [])
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
