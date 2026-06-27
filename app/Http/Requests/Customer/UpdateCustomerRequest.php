<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('id');

        return [
            'name'                  => 'sometimes|string|max:255',
            'identification'        => 'sometimes|string|in:CPF,CNPJ',
            'identification_number' => 'sometimes|integer',
            'email'                 => "sometimes|email|unique:customer,email,{$customerId}",
            'status'                => 'sometimes|boolean',
        ];
    }
}
