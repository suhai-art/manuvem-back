<?php

namespace App\Http\Requests\Api\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => ['sometimes', 'string', 'in:admin,user'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
