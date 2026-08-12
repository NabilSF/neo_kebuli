<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled via Gate in controller
    }

    public function rules(): array
    {
        return [
            'document' => [
                'required',
                'file',
                'mimes:pdf,docx',
                'max:10240', // 10 MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'document.required'  => 'File dokumen wajib diunggah.',
            'document.file'      => 'Dokumen harus berupa file yang valid.',
            'document.mimes'     => 'Format yang diizinkan hanya: PDF, DOCX.',
            'document.max'       => 'Ukuran file maksimal 10 MB.',
        ];
    }
}
