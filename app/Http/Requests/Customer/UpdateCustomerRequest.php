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
            'name'                  => 'sometimes|required|string|max:255',
            'identification'        => 'sometimes|required|string|in:CPF,CNPJ',
            'identification_number' => 'sometimes|required|integer',
            'email'                 => "sometimes|required|email|unique:customer,email,{$customerId}",
            'status'                => 'sometimes|required|boolean',
        ];
    }
}
