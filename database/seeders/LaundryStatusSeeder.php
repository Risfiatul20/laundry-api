<?php

namespace Database\Seeders;

use App\Models\LaundryStatus;
use Illuminate\Database\Seeder;

class LaundryStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Diterima',
                'sequence' => 1,
                'color' => '#3498db',
                'description' => 'Laundry telah diterima dan menunggu diproses',
            ],
            [
                'name' => 'Dicuci',
                'sequence' => 2,
                'color' => '#f39c12',
                'description' => 'Laundry sedang dalam proses pencucian',
            ],
            [
                'name' => 'Disetrika',
                'sequence' => 3,
                'color' => '#9b59b6',
                'description' => 'Laundry sedang dalam proses setrika',
            ],
            [
                'name' => 'Selesai',
                'sequence' => 4,
                'color' => '#27ae60',
                'description' => 'Laundry selesai dan siap diambil',
            ],
            [
                'name' => 'Diambil',
                'sequence' => 5,
                'color' => '#95a5a6',
                'description' => 'Laundry sudah diambil oleh pelanggan',
            ],
        ];

        foreach ($statuses as $status) {
            LaundryStatus::create($status);
        }
    }
}
