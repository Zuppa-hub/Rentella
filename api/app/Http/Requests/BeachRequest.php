<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeachRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'owner_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'location_id' => 'required|integer',
            'description' => 'nullable|string',
            'opening_date_id' => 'required|integer',
            'special_note' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'allowed_animals' => 'required|boolean',
            'type_id' => 'required|integer',
        ];
    }
}
