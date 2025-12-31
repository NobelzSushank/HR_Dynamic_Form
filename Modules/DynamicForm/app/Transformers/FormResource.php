<?php

namespace Modules\DynamicForm\Transformers;

use Modules\Core\Transformers\BaseResource;
use Modules\User\Transformers\UserResource;

class FormResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->convert($request, [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'published_at' => $this->published_at->format(config("core.date_format")),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'form_fields' => FormFieldResource::collection($this->whenLoaded('formFields')),
            'form_submissions' => FormSubmissionResource::collection($this->whenLoaded('formSubmissions')),
        ], false);
    }
}
