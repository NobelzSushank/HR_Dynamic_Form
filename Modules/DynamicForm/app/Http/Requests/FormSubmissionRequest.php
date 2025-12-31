<?php

namespace Modules\DynamicForm\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class FormSubmissionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $form = $this->route('form');
        $rules = [];
        
        foreach ($form->formFields as $field) {
            
            $base = $field->required ? ['required'] : ['nullable'];
            switch ($field->type) {
                case 'text':
                case 'textarea':
                    $base[] = 'string';
                    break;
                
                case 'number':
                    $base[] = 'numeric';
                    break;
                    
                case 'date':
                    $base[] = 'date';
                    break;
                
                case 'select':
                case 'radio':
                    $base[] = 'in:' . implode(',', $field->options->pluck('value')->all());
                    break;
                
                case 'checkbox':
                    $base[] = 'array';
                    break;
                
                case 'file':
                    $base[] = 'file';
                    break;
            }
                
            // Optional: merge custom JSON validation keys (min, max, regex, etc.)
            // You can translate $field->validation to Laravel rules here.
            
            $rules[$field->name] = $base;
        }
        $rules['form_id'] = 'required|exists:forms,id';
        return $rules;
    }


    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'form_id' => $this->route('form')->id,
        ]);
    }
}
