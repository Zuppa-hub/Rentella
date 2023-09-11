<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeachTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string|max:255',
        ];
    }
}
