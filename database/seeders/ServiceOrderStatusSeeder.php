<?php

namespace Database\Seeders;

use App\Enums\ServiceOrderStatusEnum;
use App\Models\ServiceOrderStatus;
use Illuminate\Database\Seeder;

class ServiceOrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ServiceOrderStatusEnum::cases() as $status) {
            ServiceOrderStatus::updateOrCreate(
                ['id' => $status->value],
                [
                    'name' => $status->label(),
                    'status' => true,
                    'create_date' => now(),
                    'modified_date' => null,
                ]
            );
        }
    }
}
