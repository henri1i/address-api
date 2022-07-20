<?php

namespace App\Http\Requests;

use App\Rules\Cep;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cep'             => ['required', 'digits:8', 'numeric'],
            'house_number'    => ['required', 'numeric'],
            'reference_point' => ['required', 'string'],
        ];
    }
}
