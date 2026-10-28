<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    // Owner: view, create, update,delete
    // Manager: view, create, update
    // Cashier: view, update

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; //every role
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Item $item): bool
    {
        return true; //every role
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::Owner, Role::Manager);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Item $item): bool
    {
        return true; // every role, including cashier
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->hasRole(Role::Owner);
    }

}
