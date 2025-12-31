<?php

namespace Modules\DynamicForm\Transformers;

use Modules\Core\Transformers\BaseResource;

class FormFieldOptionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return $this->convert($request, [
            'form_field_id' => $this->form_field_id,
            'value' => $this->value,
            'label' => $this->label,
            'order' => $this->order,
            'form_field' => new FormFieldResource($this->whenLoaded('formField')),
        ], false);
    }
}
