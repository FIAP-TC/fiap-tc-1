<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Customer\Models\Customer;
use App\Infrastructure\Persistence\Eloquent\Vehicule\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculeFactory extends Factory
{
    protected $model = Vehicule::class;

    public function definition(): array
    {
        return [
            'name'          => $this->faker->words(2, true),
            'plate'         => strtoupper($this->faker->unique()->bothify('???####')),
            'model'         => $this->faker->word(),
            'brand'         => $this->faker->randomElement(['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Volkswagen']),
            'years'         => $this->faker->numberBetween(2000, 2024),
            'status'        => true,
            'customer_id'   => Customer::factory(),
            'create_date'   => now()->toDateTimeString(),
            'modified_date' => now()->toDateTimeString(),
        ];
    }
}
