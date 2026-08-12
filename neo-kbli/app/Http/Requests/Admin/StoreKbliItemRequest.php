<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKbliItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized by Policy in Controller
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'digits:5', 'unique:kbli_items,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'section_id' => ['required', 'exists:kbli_sections,id'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}
