<?php

namespace Modules\DynamicForm\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\DynamicForm\Enums\FormFieldsTypeEnum;
use Modules\DynamicForm\Enums\FormStatusEnum;

class FormRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the store request.
     */
    public function store(): array
    {
        return [
            'title' => 'required|min:2|max:255|unique:forms,title',
            'description' => 'required|min:2|max:10000',
            'status' => [
                'sometimes',
                'required',
                Rule::in(FormStatusEnum::getAllValues())
            ],
            'formFields' => 'sometimes|required|array',
            'formFields.*.label' => 'required|min:2|max:100',
            'formFields.*.name' => 'required|min:2|max:100',
            'formFields.*.type' => ['required', Rule::in(FormFieldsTypeEnum::getAllValues())],
            'formFields.*.required' => 'required|boolean',
            'formFields.*.order' => 'required|integer',
            'formFields.*.validation' => 'nullable|array',
            'formFields.*.meta' => 'nullable|array',
            'formFields.*.options' => 'sometimes|nullable|array',
            'formFields.*.options.*.value' => 'required|min:2|max:100',
            'formFields.*.options.*.label' => 'required|min:2|max:100',
            'formFields.*.options.*.order' => 'required|integer',
        ];
    }

    /**
     * Get the validation rules that apply to the update request.
     */
    public function update(): array
    {
        return [
            'title' => [
                'required',
                'min:2',
                'max:255',
                Rule::unique('forms', 'title')->ignore($this->form)
            ],
            'description' => 'required|min:2|max:10000',
            'status' => [
                'sometimes',
                'required',
                Rule::in(FormStatusEnum::getAllValues())
            ],
        ];
    }
}
