<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Computer;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComputerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Computer');
    }

    public function view(AuthUser $authUser, Computer $computer): bool
    {
        return $authUser->can('View:Computer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Computer');
    }

    public function update(AuthUser $authUser, Computer $computer): bool
    {
        return $authUser->can('Update:Computer');
    }

    public function delete(AuthUser $authUser, Computer $computer): bool
    {
        return $authUser->can('Delete:Computer');
    }

    public function restore(AuthUser $authUser, Computer $computer): bool
    {
        return $authUser->can('Restore:Computer');
    }

    public function forceDelete(AuthUser $authUser, Computer $computer): bool
    {
        return $authUser->can('ForceDelete:Computer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Computer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Computer');
    }

    public function replicate(AuthUser $authUser, Computer $computer): bool
    {
        return $authUser->can('Replicate:Computer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Computer');
    }

}