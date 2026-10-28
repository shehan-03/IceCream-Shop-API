<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

// Walks every role through every endpoint and checks the status code
// against the brief's RBAC table. One row = one cell of the table.
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function matrix(): array
    {
        $rows = [];
        $expect = [
            // [method, uri, owner, manager, cashier]
            ['GET', '/api/items', 200, 200, 200],
            ['GET', '/api/items/{item}', 200, 200, 200],
            ['POST', '/api/items', 201, 201, 403],
            ['PUT', '/api/items/{item}', 200, 200, 200],
            ['PATCH', '/api/items/{item}', 200, 200, 200],
            ['DELETE', '/api/items/{item}', 204, 403, 403],
            ['GET', '/api/customers', 200, 200, 200],
            ['GET', '/api/customers/{customer}', 200, 200, 200],
            ['POST', '/api/customers', 201, 201, 201],
            ['PUT', '/api/customers/{customer}', 200, 200, 200],
            ['DELETE', '/api/customers/{customer}', 204, 403, 403],
        ];

        foreach ($expect as [$method, $uri, $owner, $manager, $cashier]) {
            foreach ([[Role::Owner, $owner], [Role::Manager, $manager], [Role::Cashier, $cashier]] as [$role, $status]) {
                $rows["{$role->value} {$method} {$uri}"] = [$role, $method, $uri, $status];
            }
        }

        return $rows;
    }

    #[DataProvider('matrix')]
    public function test_role_permissions(Role $role, string $method, string $uri, int $expectedStatus): void
    {
        $category = Category::factory()->create();
        $item = Item::factory()->for($category)->create();
        $customer = Customer::factory()->create();

        $this->actingAsRole($role);

        $uri = str_replace(['{item}', '{customer}'], [$item->id, $customer->id], $uri);
        $payload = str_contains($uri, 'items')
            ? ['category_id' => $category->id, 'name' => 'Mint Chip', 'price' => 300, 'quantity' => 5]
            : ['name' => 'Kamal', 'phone' => '0700000000'];

        $this->json($method, $uri, $payload)->assertStatus($expectedStatus);
    }
}