<?php

namespace App\Interfaces\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'identification'        => 'required|string|in:CPF,CNPJ',
            'identification_number' => 'required|integer',
            'email'                 => 'required|email|unique:customer,email',
            'status'                => 'sometimes|boolean',
        ];
    }
}
