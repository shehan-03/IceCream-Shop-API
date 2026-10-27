<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::pluck('id', 'name');

        $items = [
            ['category' => 'Chocolate', 'name' => 'Double Chocolate Scoop', 'description' => 'Dark and milk chocolate swirl', 'price' => 450, 'quantity' => 40],
            ['category' => 'Chocolate', 'name' => 'Chocolate Fudge Cone', 'description' => 'Waffle cone with fudge', 'price' => 550, 'quantity' => 25],
            ['category' => 'Vanilla', 'name' => 'Madagascar Vanilla Cup', 'description' => 'Real vanilla bean', 'price' => 400, 'quantity' => 50],
            ['category' => 'Vanilla', 'name' => 'Vanilla Sundae', 'description' => 'With caramel sauce and nuts', 'price' => 650, 'quantity' => 15],
            ['category' => 'Strawberry', 'name' => 'Strawberry Swirl', 'description' => 'Strawberry ripple in cream', 'price' => 420, 'quantity' => 30],
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $category[$item['category']],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ],
            );
        }
    }
}
