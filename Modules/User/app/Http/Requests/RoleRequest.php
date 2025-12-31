<?php

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class RoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function store(): array
    {
        return [
            'name' => 'required|min:2|max:255|unique:roles,uuid',
            'guard_name' => 'required|min:2|max:20',
            'permissions' => 'required|array',
            'premissions.*' => 'required|exists:permissions,name'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function update(): array
    {
        return [
            'name' => [
                'required',
                'min:2',
                'max:20',
                Rule::unique('roles', 'uuid')->ignore($this->role, 'uuid'),
            ],
            'guard_name' => 'required|min:2|max:20',
            'permissions' => 'required|array',
            'premissions.*' => 'required|exists:permissions,name'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'guard_name' => isset($this->guard_name) ? $this->guard_name : 'api',
        ]);
    }
}
