<?php

namespace Tests;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    // Log in as a fresh user with the given role, without going through /api/auth/login.
    // Passport::actingAs fakes a valid token for the "api" guard.
    protected function actingAsRole(Role $role): User
    {
        $user = User::factory()->role($role)->create();
        Passport::actingAs($user);

        return $user;
    }
}