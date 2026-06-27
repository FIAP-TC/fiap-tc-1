<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'username'    => $this->faker->unique()->userName(),
            'password'    => Hash::make('password'),
            'role_id'     => Role::factory(),
            'status'      => 1,
            'create_date' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => ['role_id' => 1]);
    }

    public function gerente(): static
    {
        return $this->state(fn() => ['role_id' => 2]);
    }

    public function mecanico(): static
    {
        return $this->state(fn() => ['role_id' => 3]);
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['status' => 0]);
    }
}
