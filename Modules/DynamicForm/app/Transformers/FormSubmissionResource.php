<?php

namespace Modules\DynamicForm\Transformers;

use Modules\Core\Transformers\BaseResource;
use Modules\User\Transformers\UserResource;

class FormSubmissionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return $this->convert($request, [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'form' => new FormResource($this->whenLoaded('form')),
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'form_submission_answers' => FormSubmissionAnswerResource::collection($this->whenLoaded('formSubmissionAnswers')),
        ], false);
    }
}
