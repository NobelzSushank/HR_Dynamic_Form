<?php

namespace Modules\DynamicForm\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\DynamicForm\Enums\FormFieldsTypeEnum;

class FormFieldRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the store request.
     */
    public function rules(): array
    {
        return [
            'form_id' => 'required|exists:forms,id',
            'label' => 'required|min:2|max:100',
            'name' => 'required|min:2|max:100',
            'type' => ['required', Rule::in(FormFieldsTypeEnum::getAllValues())],
            'required' => 'required|boolean',
            'order' => 'required|integer',
            'validation' => 'nullable|array',
            'meta' => 'nullable|array',
            'options' => 'sometimes|nullable|array',
            'options.*.value' => 'required|min:2|max:100',
            'options.*.label' => 'required|min:2|max:100',
            'options.*.order' => 'required|integer',
        ];
    }

     protected function prepareForValidation(): void
    {
        $this->merge([
            'form_id' => $this->form,
        ]);
    }

    
}
