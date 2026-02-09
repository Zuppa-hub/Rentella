<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeachRequest extends FormRequest
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
            'owner_id' => 'required|integer|exists:owners,id',
            'name' => 'required|string|min:3|max:255',
            'location_id' => 'required|integer|exists:cities_location,id',
            'description' => 'nullable|string|max:1000',
            'opening_date_id' => 'required|integer|exists:opening_dates,id',
            'special_note' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'allowed_animals' => 'required|boolean',
            'type_id' => 'required|integer|exists:beach_types,id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'owner_id.required' => 'Beach must belong to an owner',
            'owner_id.exists' => 'Owner not found',
            'name.required' => 'Beach name is required',
            'name.min' => 'Beach name must be at least 3 characters',
            'location_id.required' => 'Beach location is required',
            'location_id.exists' => 'Location not found',
            'latitude.required' => 'Beach latitude is required',
            'latitude.numeric' => 'Latitude must be a valid number',
            'latitude.between' => 'Latitude must be between -90 and 90',
            'longitude.required' => 'Beach longitude is required',
            'longitude.numeric' => 'Longitude must be a valid number',
            'longitude.between' => 'Longitude must be between -180 and 180',
            'type_id.required' => 'Beach type is required',
            'type_id.exists' => 'Beach type not found',
        ];
    }
}
