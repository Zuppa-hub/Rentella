<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OwnerRequest extends FormRequest
{
    public function authorize()
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return false;
        }
        $admins = config('app.admin_emails', []);
        return in_array($authUser->email, $admins);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email',
            'adress' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ];
    }
}
