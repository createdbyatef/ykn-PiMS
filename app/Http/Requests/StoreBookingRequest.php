<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && in_array($this->user()->role, ['staff', 'admin'], true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'request_type' => ['required', Rule::in(array_keys(Booking::REQUEST_TYPES))],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
            'destination' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:2000'],
            'driver_license_no' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf(fn () => $this->input('request_type') === Booking::TYPE_VEHICLE_ONLY),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'request_type.required' => 'Sila pilih jenis permohonan.',
            'start_datetime.required' => 'Tarikh dan masa mula perjalanan wajib diisi.',
            'end_datetime.required' => 'Tarikh dan masa tamat perjalanan wajib diisi.',
            'end_datetime.after' => 'Tarikh dan masa tamat mestilah selepas tarikh dan masa mula.',
            'destination.required' => 'Destinasi perjalanan wajib diisi.',
            'purpose.required' => 'Tujuan rasmi perjalanan wajib diisi.',
            'driver_license_no.required' => 'Nombor lesen memandu wajib diisi bagi permohonan Kenderaan Sahaja (Pandu Sendiri).',
        ];
    }
}
