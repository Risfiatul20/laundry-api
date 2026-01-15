<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Tunai',
                'description' => 'Pembayaran langsung dengan uang tunai',
            ],
            [
                'name' => 'Transfer Bank',
                'description' => 'Pembayaran via transfer bank',
            ],
            [
                'name' => 'QRIS',
                'description' => 'Pembayaran dengan scan QR code',
            ],
            [
                'name' => 'E-Wallet',
                'description' => 'Pembayaran via GoPay, OVO, Dana, dll',
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
