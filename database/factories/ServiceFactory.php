<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Service\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name'          => $this->faker->randomElement([
                'Troca de óleo',
                'Alinhamento',
                'Balanceamento',
                'Revisão completa',
                'Troca de pastilha',
                'Limpeza de bico',
            ]) . ' ' . $this->faker->word(),
            'value'         => $this->faker->randomFloat(2, 50, 2000),
            'status'        => true,
            'create_date'   => now()->toDateTimeString(),
            'modified_date' => now()->toDateTimeString(),
        ];
    }

    /** Estado para serviço inativo (soft-deleted). */
    public function inactive(): static
    {
        return $this->state(['status' => false]);
    }
}
