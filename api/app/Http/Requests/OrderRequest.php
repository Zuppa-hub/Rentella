<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'umbrella_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'user_id' => 'required|numeric',
            'price_id' => 'required|numeric',
        ];
    }
}
