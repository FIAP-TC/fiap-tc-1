<?php

namespace App\Interfaces\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'role_id'  => ['required', 'integer', 'exists:role,id'],
            'status'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'O username é obrigatório.',
            'username.unique'   => 'Este username já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min'      => 'A senha deve ter no mínimo 6 caracteres.',
            'role_id.required'  => 'A role é obrigatória.',
            'role_id.exists'    => 'A role informada não existe.',
        ];
    }
}
