<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeachFilterRequest extends FormRequest
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
            'cityId' => 'nullable|integer|exists:cities_location,id',
            'allowed_animals' => 'nullable|in:yes,no',
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0|gte:minPrice',
            'minLatitude' => 'nullable|numeric|between:-90,90',
            'maxLatitude' => 'nullable|numeric|between:-90,90|gte:minLatitude',
            'minLongitude' => 'nullable|numeric|between:-180,180',
            'maxLongitude' => 'nullable|numeric|between:-180,180|gte:minLongitude',
            'page' => 'nullable|integer|min:1',
            'perPage' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cityId.exists' => 'City location not found',
            'allowed_animals.in' => 'allowed_animals must be either "yes" or "no"',
            'minPrice.numeric' => 'Minimum price must be a valid number',
            'maxPrice.numeric' => 'Maximum price must be a valid number',
            'maxPrice.gte' => 'Maximum price must be greater than or equal to minimum price',
            'minLatitude.between' => 'Latitude must be between -90 and 90',
            'maxLatitude.between' => 'Latitude must be between -90 and 90',
            'maxLatitude.gte' => 'Max latitude must be greater than or equal to min latitude',
            'minLongitude.between' => 'Longitude must be between -180 and 180',
            'maxLongitude.between' => 'Longitude must be between -180 and 180',
            'maxLongitude.gte' => 'Max longitude must be greater than or equal to min longitude',
            'page.integer' => 'Page must be a valid integer',
            'perPage.integer' => 'Per page must be a valid integer',
            'perPage.max' => 'Per page cannot exceed 100',
        ];
    }
}
