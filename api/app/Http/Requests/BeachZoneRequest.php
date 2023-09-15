<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeachZoneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price_id' => 'required|exists:prices,id',
            'description' => 'nullable|string|max:255',
            'special_note' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'beach_id' => 'required|exists:beaches,id',
        ];
    }
}
