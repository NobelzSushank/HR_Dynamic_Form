<?php

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Modules\Core\Http\Requests\BaseRequest;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function store(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:roles,uuid'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function update(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user)
            ],
            'role_id' => 'required|exists:roles,uuid'
        ];
    }
}
