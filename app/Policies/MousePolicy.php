<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Mouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class MousePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Mouse');
    }

    public function view(AuthUser $authUser, Mouse $mouse): bool
    {
        return $authUser->can('View:Mouse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Mouse');
    }

    public function update(AuthUser $authUser, Mouse $mouse): bool
    {
        return $authUser->can('Update:Mouse');
    }

    public function delete(AuthUser $authUser, Mouse $mouse): bool
    {
        return $authUser->can('Delete:Mouse');
    }

    public function restore(AuthUser $authUser, Mouse $mouse): bool
    {
        return $authUser->can('Restore:Mouse');
    }

    public function forceDelete(AuthUser $authUser, Mouse $mouse): bool
    {
        return $authUser->can('ForceDelete:Mouse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Mouse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Mouse');
    }

    public function replicate(AuthUser $authUser, Mouse $mouse): bool
    {
        return $authUser->can('Replicate:Mouse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Mouse');
    }

}