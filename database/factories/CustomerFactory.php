<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $isCpf = $this->faker->boolean();

        return [
            'name'                  => $this->faker->name(),
            'identification'        => $isCpf ? 'CPF' : 'CNPJ',
            'identification_number' => $isCpf
                ? (int) $this->faker->numerify('###########')
                : (int) $this->faker->numerify('##############'),
            'email'                 => $this->faker->unique()->safeEmail(),
            'status'                => true,
            'create_date'           => now()->toDateTimeString(),
            'modified_date'         => now()->toDateTimeString(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => false]);
    }
}
