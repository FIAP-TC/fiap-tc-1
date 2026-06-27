<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->randomElement(['Administrador', 'Gerente', 'Mecânico']),
            'status'      => 'ativo',
            'create_date' => now(),
        ];
    }

    public function administrador(): static
    {
        return $this->state(['id' => 1, 'name' => 'Administrador']);
    }

    public function gerente(): static
    {
        return $this->state(['id' => 2, 'name' => 'Gerente']);
    }

    public function mecanico(): static
    {
        return $this->state(['id' => 3, 'name' => 'Mecânico']);
    }
}
