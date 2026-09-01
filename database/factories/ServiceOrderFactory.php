<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models\ServiceOrder;
use App\Infrastructure\Persistence\Eloquent\Vehicule\Models\Vehicule;
use App\Infrastructure\Persistence\Eloquent\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceOrderFactory extends Factory
{
    protected $model = ServiceOrder::class;

    public function definition(): array
    {
        return [
            'users_id'      => User::factory(),
            'users_role_id' => 1,
            'vehicules_id'  => Vehicule::factory(),
            'order_value'   => 0.00,
            'time_average'  => $this->faker->optional()->randomFloat(2, 0.5, 8),
            'status'        => true,
            'create_date'   => now()->toDateTimeString(),
            'modified_date' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => false]);
    }
}
