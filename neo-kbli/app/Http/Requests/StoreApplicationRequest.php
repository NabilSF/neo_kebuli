<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kbli_item_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('kbli_items', 'id')->where('is_active', true)
            ],
            'product_name' => 'required|string|max:200',
            'product_material' => 'required|string',
            'production_process' => 'required|string',
            'product_description' => 'nullable|string',
            'saker_address' => 'required|string',
            'document' => 'required|file|mimes:pdf,jpg,png|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'kbli_item_id.required' => 'Klasifikasi KBLI wajib dipilih.',
            'kbli_item_id.exists' => 'Klasifikasi KBLI tidak valid.',
            'document.mimes' => 'Dokumen lampiran harus berformat PDF, JPG, atau PNG.',
            'document.max' => 'Ukuran file dokumen tidak boleh melebihi 5MB.',
        ];
    }
}
