<?php

namespace Modules\DynamicForm\Transformers;

use Modules\Core\Transformers\BaseResource;

class FormSubmissionAnswerResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return $this->convert($request, [
            'submission_id' => $this->submission_id,
            'form_submission' => new FormSubmissionResource($this->whenLoaded('formSubmission')),
            'form_field_id' => $this->form_field_id,
            'form_field' => new FormFieldResource($this->whenLoaded('formField')),
            'value_text' => $this->value_text,
            'value_number' => $this->value_number,
            'value_date' => $this->value_date,
            'value_json' => $this->value_json,
        ], false);
    }
}
