<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeachRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'owner_id' => 'sometimes|required|integer|exists:owners,id',
            'name' => 'sometimes|required|string|min:3|max:255',
            'location_id' => 'sometimes|required|integer|exists:cities_location,id',
            'description' => 'nullable|string|max:1000',
            'opening_date_id' => 'sometimes|required|integer|exists:opening_dates,id',
            'special_note' => 'nullable|string|max:500',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'allowed_animals' => 'sometimes|required|boolean',
            'type_id' => 'sometimes|required|integer|exists:beach_types,id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'owner_id.exists' => 'Owner not found',
            'name.min' => 'Beach name must be at least 3 characters',
            'name.max' => 'Beach name must not exceed 255 characters',
            'location_id.exists' => 'Location not found',
            'opening_date_id.exists' => 'Opening date not found',
            'latitude.numeric' => 'Latitude must be a valid number',
            'latitude.between' => 'Latitude must be between -90 and 90',
            'longitude.numeric' => 'Longitude must be a valid number',
            'longitude.between' => 'Longitude must be between -180 and 180',
            'allowed_animals.boolean' => 'Allowed animals must be true or false',
            'type_id.exists' => 'Beach type not found',
        ];
    }
}
