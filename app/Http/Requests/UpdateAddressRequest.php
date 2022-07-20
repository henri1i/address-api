<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'house_number'    => 'numeric',
            'cep'             => ['numeric', 'digits:8'],
            'reference_point' => 'string',
        ];
    }
}
