<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
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
        /** @var Vehicle|null $vehicle */
        $vehicle = $this->route('vehicle');

        return [
            'plate_number' => [
                'required',
                'string',
                'max:25',
                Rule::unique('vehicles', 'plate_number')->ignore($vehicle?->id),
            ],
            'model' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(Vehicle::TYPES)],
            'capacity' => ['required', 'integer', 'min:1', 'max:60'],
            'current_odometer' => ['required', 'integer', 'min:0', 'max:9999999'],
            'status' => ['required', Rule::in(array_keys(Vehicle::STATUSES))],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('plate_number')) {
            $this->merge([
                'plate_number' => strtoupper(trim((string) $this->input('plate_number'))),
            ]);
        }
    }
}
