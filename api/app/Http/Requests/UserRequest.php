<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize() : bool
    {
        return true; // return true to allow the validation
    }

    public function rules() : array
    {
        return [
            'email' => 'required|email|unique:users',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
        ];
    }
}
