<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ApplicationStatus;

/**
 * Class UpdateApplicationStatusRequest
 * 
 * Responsibility Boundary:
 * - Responsible ONLY for validating the integrity of the incoming HTTP payload.
 * - Enforces that 'status' is a valid ApplicationStatus enum value and 'notes' is provided when mandatory.
 * - Does NOT contain business rules or workflow transition logic (e.g., it does not check if an APPROVED application can be REJECTED).
 * - Workflow decisions are strictly delegated to the Service layer utilizing the ApplicationStatus domain methods.
 */
class UpdateApplicationStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization remains handled exclusively by route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ApplicationStatus::class)],
            'notes' => [
                'required_if:status,' . ApplicationStatus::REJECTED->value . ',' . ApplicationStatus::NEED_REVISION->value,
                'nullable',
                'string',
                'max:1000'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status target wajib diisi.',
            'status.enum' => 'Status yang dipilih bukan merupakan status alur kerja yang valid.',
            'notes.required_if' => 'Catatan tinjauan wajib diisi saat menolak atau meminta revisi permohonan.',
            'notes.max' => 'Catatan tinjauan tidak boleh melebihi 1000 karakter.',
        ];
    }
}
