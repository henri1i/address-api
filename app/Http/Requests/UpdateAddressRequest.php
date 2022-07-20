<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'house_number'    => 'numeric',
            'cep'             => ['numeric', 'digits:8'],
            'reference_point' => 'string',
        ];
    }
}
