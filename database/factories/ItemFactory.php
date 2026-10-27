<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(2, true).' Scoop',
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100, 1000),
            'quantity' => fake()->numberBetween(0, 100),
        ];
    }
}
