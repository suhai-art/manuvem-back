<?php

namespace App\Http\Requests\Api\Roles;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUpdateRoleRequest extends FormRequest
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
        return [
            'name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $this->input('guard_name', config('auth.defaults.guard', 'web')))
                    ->ignore($this->route('id')),
            ],
            'guard_name'    => ['sometimes', 'string', 'max:255'],
            'permission'    => ['sometimes', 'array'],
            'permission.*'  => ['integer', 'exists:permissions,id'],
        ];
    }
}
