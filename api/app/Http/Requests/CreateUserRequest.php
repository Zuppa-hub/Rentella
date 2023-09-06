<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Restituisci true per consentire la validazione
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
        ];
    }
}
