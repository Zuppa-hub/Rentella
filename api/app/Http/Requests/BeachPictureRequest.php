<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeachPictureRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'photo' => 'required|url',
            'beach_id' => 'required|exists:beaches,id',
        ];
    }
}
