<?php

namespace App\Interfaces\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            // unique ignora o próprio usuário ao atualizar
            'username' => ['sometimes', 'string', 'max:255', "unique:users,username,{$userId}"],
            'password' => ['sometimes', 'string', 'min:6'],
            'role_id'  => ['sometimes', 'integer', 'exists:role,id'],
            'status'   => ['sometimes', 'boolean'],
        ];
    }
}
