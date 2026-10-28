<?php

namespace Tests\Unit;

use App\Enums\Role;
use App\Models\Customer;
use App\Models\Item;
use App\Models\User;
use App\Policies\CustomerPolicy;
use App\Policies\ItemPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

// Pure unit tests: no database, no HTTP. They call the policy methods directly with in-memory models.
class PolicyTest extends TestCase
{
    public static function roles(): array
    {
        return [
            'owner' => [Role::Owner,
                ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => true],
                ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => true]],
            'manager' => [Role::Manager,
                ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => false],
                ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => false]],
            'cashier' => [Role::Cashier,
                ['viewAny' => true, 'create' => false, 'update' => true, 'delete' => false],
                ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => false]],
        ];
    }

    #[DataProvider('roles')]
    public function test_policies_match_the_permission_matrix(Role $role, array $items, array $customers): void
    {
        $user = new User(['role' => $role]);
        $itemPolicy = new ItemPolicy;
        $customerPolicy = new CustomerPolicy;

        $this->assertSame($items['viewAny'], $itemPolicy->viewAny($user));
        $this->assertSame($items['create'], $itemPolicy->create($user));
        $this->assertSame($items['update'], $itemPolicy->update($user, new Item));
        $this->assertSame($items['delete'], $itemPolicy->delete($user, new Item));

        $this->assertSame($customers['viewAny'], $customerPolicy->viewAny($user));
        $this->assertSame($customers['create'], $customerPolicy->create($user));
        $this->assertSame($customers['update'], $customerPolicy->update($user, new Customer));
        $this->assertSame($customers['delete'], $customerPolicy->delete($user, new Customer));
    }
}