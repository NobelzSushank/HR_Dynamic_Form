<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Modules\Core\Http\Requests\BaseRequest;

class UserPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            "old_password" => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
