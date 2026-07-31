<?php

namespace App\Http\Requests\Api\Items;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUpdateItemRequest extends FormRequest
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
            'internal_code'         => ['required', 'string'],
            'name'                  => ['required', 'string'],
            'description'           => ['required', 'string'],
            'default_unit_price'    => ['required', 'numeric', 'min:0'],
        ];
    }
}
