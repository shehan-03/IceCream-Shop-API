<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    private Category $chocolate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chocolate = Category::factory()->create(['name' => 'Chocolate']);
        $this->actingAsRole(Role::Owner);
    }

    public function test_owner_can_create_show_update_and_delete_an_item(): void
    {
        $id = $this->postJson('/api/items', [
            'category_id' => $this->chocolate->id,
            'name' => 'Choc Cone',
            'price' => 450.5,
            'quantity' => 10,
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Choc Cone')
            ->assertJsonPath('data.price', 450.5)
            ->assertJsonPath('data.category.name', 'Chocolate')
            ->json('data.id');

        $this->getJson("/api/items/{$id}")->assertOk()->assertJsonPath('data.id', $id);

        // PATCH changes only what is sent.
        $this->patchJson("/api/items/{$id}", ['quantity' => 3])
            ->assertOk()
            ->assertJsonPath('data.quantity', 3)
            ->assertJsonPath('data.name', 'Choc Cone');

        $this->deleteJson("/api/items/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('items', ['id' => $id]);
        $this->getJson("/api/items/{$id}")->assertNotFound()->assertJson(['message' => 'Resource not found.']);
    }

    public function test_create_validates_input(): void
    {
        $this->postJson('/api/items', ['name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category_id', 'name', 'price', 'quantity']);

        $this->postJson('/api/items', [
            'category_id' => 999999, 'name' => 'Ghost', 'price' => -1, 'quantity' => 1.5,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['category_id', 'price', 'quantity']);
    }

    public function test_items_can_be_searched_by_name_and_paginated(): void
    {
        Item::factory()->for($this->chocolate)->create(['name' => 'Chocolate Fudge']);
        Item::factory()->for($this->chocolate)->create(['name' => 'Dark Chocolate Cup']);
        Item::factory()->for($this->chocolate)->create(['name' => 'Vanilla Cup']);

        $this->getJson('/api/items?search=chocolate')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);

        $this->getJson('/api/items?per_page=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.last_page', 3);

        $this->getJson('/api/items?search=strawberry')->assertOk()->assertJsonCount(0, 'data');
    }
    public function test_categories_are_listed_alphabetically(): void
    {
        Category::factory()->create(['name' => 'Vanilla']);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Chocolate')
            ->assertJsonPath('data.1.name', 'Vanilla');
    }

}