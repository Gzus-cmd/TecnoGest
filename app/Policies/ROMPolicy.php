<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ROM;
use Illuminate\Auth\Access\HandlesAuthorization;

class ROMPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ROM');
    }

    public function view(AuthUser $authUser, ROM $rOM): bool
    {
        return $authUser->can('View:ROM');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ROM');
    }

    public function update(AuthUser $authUser, ROM $rOM): bool
    {
        return $authUser->can('Update:ROM');
    }

    public function delete(AuthUser $authUser, ROM $rOM): bool
    {
        return $authUser->can('Delete:ROM');
    }

    public function restore(AuthUser $authUser, ROM $rOM): bool
    {
        return $authUser->can('Restore:ROM');
    }

    public function forceDelete(AuthUser $authUser, ROM $rOM): bool
    {
        return $authUser->can('ForceDelete:ROM');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ROM');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ROM');
    }

    public function replicate(AuthUser $authUser, ROM $rOM): bool
    {
        return $authUser->can('Replicate:ROM');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ROM');
    }

}