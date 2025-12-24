<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OS;
use Illuminate\Auth\Access\HandlesAuthorization;

class OSPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OS');
    }

    public function view(AuthUser $authUser, OS $oS): bool
    {
        return $authUser->can('View:OS');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OS');
    }

    public function update(AuthUser $authUser, OS $oS): bool
    {
        return $authUser->can('Update:OS');
    }

    public function delete(AuthUser $authUser, OS $oS): bool
    {
        return $authUser->can('Delete:OS');
    }

    public function restore(AuthUser $authUser, OS $oS): bool
    {
        return $authUser->can('Restore:OS');
    }

    public function forceDelete(AuthUser $authUser, OS $oS): bool
    {
        return $authUser->can('ForceDelete:OS');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OS');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OS');
    }

    public function replicate(AuthUser $authUser, OS $oS): bool
    {
        return $authUser->can('Replicate:OS');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OS');
    }

}