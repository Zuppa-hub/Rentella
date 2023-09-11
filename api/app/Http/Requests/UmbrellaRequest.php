<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UmbrellaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zone_id' => 'required|exists:beach_zones,id',
            'number' => 'required|numeric',
            'special_note' => 'nullable|string',
        ];
    }
}
