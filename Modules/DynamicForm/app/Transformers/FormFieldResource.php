<?php

namespace Modules\DynamicForm\Transformers;

use Modules\Core\Transformers\BaseResource;

class FormFieldResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return $this->convert($request,[
            'form_id' => $this->form_id,
            'form' => new FormResource($this->whenLoaded('form')),
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this->type,
            'required' => $this->required,
            'order' => $this->order,
            'validation' => $this->validation,
            'meta' => $this->meta,
            'form_field_options' => FormFieldOptionResource::collection($this->whenLoaded('formFieldOptions')),
            'form_submission_answers' => FormSubmissionAnswerResource::collection($this->whenLoaded('formSubmissionAnswers')),
        ], false);
    }
}
