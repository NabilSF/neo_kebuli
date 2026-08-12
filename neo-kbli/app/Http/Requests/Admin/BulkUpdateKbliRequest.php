<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateKbliRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bulkUpdate', \App\Models\KbliItem::class);
    }

    public function rules(): array
    {
        return [
            'bulk_ids' => ['required', 'array', 'min:1'],
            'bulk_ids.*' => ['required', 'integer', 'exists:kbli_items,id'],
            'bulk_action' => ['required', 'in:activate,deactivate'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'bulk_ids.required' => 'Pilih setidaknya satu data KBLI.',
            'bulk_ids.min' => 'Pilih setidaknya satu data KBLI.',
            'bulk_ids.*.exists' => 'Terdapat ID KBLI yang tidak valid.',
            'bulk_action.required' => 'Pilih aksi yang valid.',
            'bulk_action.in' => 'Pilih aksi activate atau deactivate.',
        ];
    }
}
