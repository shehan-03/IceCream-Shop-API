<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Real tokens need a personal access client (done once per install in real life).
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client', 'users');

        User::factory()->role(Role::Manager)->create(['email' => 'manager@icecream.test']);
    }

    public function test_user_can_log_in_and_receives_a_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'MANAGER@icecream.test', // case-insensitive
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token', 'token_type', 'expires_at', 'user' => ['id', 'email', 'role']]])
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.role', 'manager')
            ->assertJsonMissingPath('data.user.password');
    }

    public function test_wrong_password_and_unknown_email_get_the_same_401(): void
    {
        $this->postJson('/api/auth/login', ['email' => 'manager@icecream.test', 'password' => 'wrong'])
            ->assertUnauthorized()
            ->assertJson(['message' => 'Invalid email or password.']);

        $this->postJson('/api/auth/login', ['email' => 'nobody@icecream.test', 'password' => 'password'])
            ->assertUnauthorized()
            ->assertJson(['message' => 'Invalid email or password.']);
    }

    public function test_login_validates_input(): void
    {
        $this->postJson('/api/auth/login', ['email' => 'not-an-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_protected_routes_require_a_token(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
        $this->getJson('/api/items')->assertUnauthorized();
        $this->withToken('garbage')->getJson('/api/customers')->assertUnauthorized();
    }

    public function test_logout_revokes_the_token(): void
    {
        $token = $this->postJson('/api/auth/login', [
            'email' => 'manager@icecream.test', 'password' => 'password',
        ])->json('data.token');

        $this->withToken($token)->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'manager@icecream.test');

        $this->withToken($token)->postJson('/api/auth/logout')->assertOk();

        // The guard caches the user between requests inside one test; reset it
        // so the next request is authenticated from scratch, like a real client.
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/auth/me')->assertUnauthorized();
    }
}