<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, Institution $institution): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Institution $institution): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Institution $institution): bool
    {
        return $user->is_admin;
    }

    public function review(User $user, Institution $institution): bool
    {
        return $user->is_admin;
    }
}
