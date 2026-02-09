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
        $userId = $this->route('id');
        return [
            'email' => 'required|email|unique:users,email,' . $userId,
            'firstName' => 'required|string',
            'lastName' => 'required|string',
        ];
    }
}
