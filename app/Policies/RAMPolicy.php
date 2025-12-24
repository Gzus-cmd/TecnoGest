<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RAM;
use Illuminate\Auth\Access\HandlesAuthorization;

class RAMPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RAM');
    }

    public function view(AuthUser $authUser, RAM $rAM): bool
    {
        return $authUser->can('View:RAM');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RAM');
    }

    public function update(AuthUser $authUser, RAM $rAM): bool
    {
        return $authUser->can('Update:RAM');
    }

    public function delete(AuthUser $authUser, RAM $rAM): bool
    {
        return $authUser->can('Delete:RAM');
    }

    public function restore(AuthUser $authUser, RAM $rAM): bool
    {
        return $authUser->can('Restore:RAM');
    }

    public function forceDelete(AuthUser $authUser, RAM $rAM): bool
    {
        return $authUser->can('ForceDelete:RAM');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RAM');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RAM');
    }

    public function replicate(AuthUser $authUser, RAM $rAM): bool
    {
        return $authUser->can('Replicate:RAM');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RAM');
    }

}