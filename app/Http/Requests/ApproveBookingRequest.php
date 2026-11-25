<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;

class ApproveBookingRequest extends FormRequest
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
        /** @var Booking|null $booking */
        $booking = $this->route('booking');

        return [
            'vehicle_id' => [
                $booking && $booking->requiresVehicle() ? 'required' : 'nullable',
                'integer',
                'exists:vehicles,id',
            ],
            'pilot_id' => [
                $booking && $booking->requiresPilot() ? 'required' : 'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => __('Sila pilih kenderaan untuk diluluskan.'),
            'pilot_id.required' => __('Sila pilih Pilot HQ untuk ditugaskan.'),
        ];
    }
}
