<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Olivia Owner', 'email' => 'owner@icecream.test', 'role' => Role::Owner],
            ['name' => 'Mark Manager', 'email' => 'manager@icecream.test', 'role' => Role::Manager],
            ['name' => 'Cathy Cashier', 'email' => 'cashier@icecream.test', 'role' => Role::Cashier],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [...$user, 'password' => 'password'],
            );
        }
    }
}
