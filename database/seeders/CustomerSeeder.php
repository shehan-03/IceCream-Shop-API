<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $customers = [
            ['name' => 'Nimal Perera', 'email' => 'nimal@example.com', 'phone' => '0771234567', 'address' => '12 Galle Road, Colombo'],
            ['name' => 'Ayesha Fernando', 'email' => 'ayesha@example.com', 'phone' => '0719876543', 'address' => null],
            ['name' => 'Kasun Silva', 'email' => null, 'phone' => '0751112233', 'address' => null],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['name' => $customer['name'], 'phone' => $customer['phone']],
                $customer,
            );
        }
    }
}
