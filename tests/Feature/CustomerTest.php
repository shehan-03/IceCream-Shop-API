<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_create_view_and_update_a_customer(): void
    {
        $this->actingAsRole(Role::Cashier);

        $id = $this->postJson('/api/customers', [
            'name' => 'Nimal Perera',
            'email' => 'Nimal@Example.com',
            'phone' => '0771234567',
        ])->assertCreated()
            ->assertJsonPath('data.email', 'nimal@example.com') // normalised to lower case
            ->json('data.id');

        $this->getJson("/api/customers/{$id}")->assertOk()->assertJsonPath('data.name', 'Nimal Perera');

        $this->putJson("/api/customers/{$id}", ['address' => 'Colombo'])
            ->assertOk()
            ->assertJsonPath('data.address', 'Colombo')
            ->assertJsonPath('data.phone', '0771234567');
    }

    public function test_email_must_be_unique_but_a_customer_can_keep_their_own(): void
    {
        $this->actingAsRole(Role::Owner);
        $existing = Customer::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/customers', ['name' => 'Copy', 'phone' => '1', 'email' => 'TAKEN@example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->patchJson("/api/customers/{$existing->id}", ['email' => 'taken@example.com', 'name' => 'Renamed'])
            ->assertOk();
    }

    public function test_create_requires_name_and_phone(): void
    {
        $this->actingAsRole(Role::Manager);

        $this->postJson('/api/customers', ['email' => 'bad'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'phone', 'email']);
    }

    public function test_customers_can_be_searched_by_name_email_or_phone(): void
    {
        $this->actingAsRole(Role::Owner);
        Customer::factory()->create(['name' => 'Ayesha', 'email' => 'ayesha@example.com', 'phone' => '0711111111']);
        Customer::factory()->create(['name' => 'Kasun', 'email' => 'kasun@example.com', 'phone' => '0752222222']);

        $this->getJson('/api/customers?search=ayesha')->assertJsonCount(1, 'data');
        $this->getJson('/api/customers?search=kasun@')->assertJsonCount(1, 'data');
        $this->getJson('/api/customers?search=075')->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Kasun');
    }

    public function test_owner_can_delete_a_customer(): void
    {
        $this->actingAsRole(Role::Owner);
        $customer = Customer::factory()->create();

        $this->deleteJson("/api/customers/{$customer->id}")->assertNoContent();
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}