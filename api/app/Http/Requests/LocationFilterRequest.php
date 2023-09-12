<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minLatitude' => 'numeric',
            'maxLatitude' => 'numeric',  'gt:minLatitude',
            'minLongitude' => 'numeric',
            'maxLongitude' => 'numeric', 'gt:minLongitude',
            'myLatitude' => 'numeric',
            'myLongitude' => 'numeric',
        ];
    }
}
