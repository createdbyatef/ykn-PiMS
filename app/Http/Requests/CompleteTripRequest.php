<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;

class CompleteTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        /** @var Booking|null $booking */
        $booking = $this->route('booking');

        if (! $user || ! $booking) {
            return false;
        }

        return $user->isAdmin() || ($user->isPilot() && $booking->pilot_id === $user->id);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Booking|null $booking */
        $booking = $this->route('booking');
        $requiresOdometer = $booking && $booking->vehicle_id !== null;

        return [
            'odometer_start' => [
                $requiresOdometer ? 'required' : 'nullable',
                'integer',
                'min:0',
            ],
            'odometer_end' => [
                $requiresOdometer ? 'required' : 'nullable',
                'integer',
                'gt:odometer_start',
            ],
            'pilot_remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'odometer_start.required' => __('Bacaan Odometer Mula wajib diisi.'),
            'odometer_end.required' => __('Bacaan Odometer Tamat wajib diisi.'),
            'odometer_end.gt' => __('Odometer Tamat mestilah melebihi Odometer Mula.'),
        ];
    }
}
