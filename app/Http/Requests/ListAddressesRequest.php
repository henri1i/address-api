<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListAddressesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['numeric', 'min:5', 'max:30'],
        ];
    }
}
