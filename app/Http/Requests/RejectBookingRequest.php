<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RejectBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', Rule::in(array_keys(Booking::REJECTION_REASONS))],
            'rejection_remarks' => [
                'nullable',
                'string',
                'max:500',
                Rule::requiredIf(fn () => $this->input('rejection_reason') === Booking::REJECTION_OTHER),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rejection_reason.required' => __('Sila pilih sebab penolakan permohonan.'),
            'rejection_remarks.required' => __('Sila nyatakan catatan tambahan apabila memilih sebab Lain-lain.'),
        ];
    }
}
