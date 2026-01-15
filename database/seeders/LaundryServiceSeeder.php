<?php

namespace Database\Seeders;

use App\Models\LaundryService;
use Illuminate\Database\Seeder;

class LaundryServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Cuci Kering',
                'price_per_kg' => 5000,
                'estimated_hours' => 24,
                'description' => 'Layanan cuci dan keringkan saja tanpa setrika',
            ],
            [
                'name' => 'Cuci Setrika',
                'price_per_kg' => 8000,
                'estimated_hours' => 48,
                'description' => 'Layanan cuci lengkap dengan setrika rapi',
            ],
            [
                'name' => 'Cuci Setrika Express',
                'price_per_kg' => 12000,
                'estimated_hours' => 6,
                'description' => 'Layanan cuci setrika kilat selesai dalam 6 jam',
            ],
            [
                'name' => 'Setrika Saja',
                'price_per_kg' => 4000,
                'estimated_hours' => 24,
                'description' => 'Layanan setrika saja untuk pakaian bersih',
            ],
            [
                'name' => 'Dry Clean',
                'price_per_kg' => 15000,
                'estimated_hours' => 72,
                'description' => 'Layanan dry cleaning untuk pakaian khusus',
            ],
        ];

        foreach ($services as $service) {
            LaundryService::create($service);
        }
    }
}
