<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Chocolate' => 'Rich cocoa based flavors',
            'Vanilla' => 'Classic vanilla bean flavors',
            'Strawberry' => 'Fresh berry flavors',
        ];

        foreach ($categories as $name => $description) {
            Category::updateOrCreate(['name' => $name], ['description' => $description]);
        }
    }
}
