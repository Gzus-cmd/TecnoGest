<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Peripheral;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeripheralPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Peripheral');
    }

    public function view(AuthUser $authUser, Peripheral $peripheral): bool
    {
        return $authUser->can('View:Peripheral');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Peripheral');
    }

    public function update(AuthUser $authUser, Peripheral $peripheral): bool
    {
        return $authUser->can('Update:Peripheral');
    }

    public function delete(AuthUser $authUser, Peripheral $peripheral): bool
    {
        return $authUser->can('Delete:Peripheral');
    }

    public function restore(AuthUser $authUser, Peripheral $peripheral): bool
    {
        return $authUser->can('Restore:Peripheral');
    }

    public function forceDelete(AuthUser $authUser, Peripheral $peripheral): bool
    {
        return $authUser->can('ForceDelete:Peripheral');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Peripheral');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Peripheral');
    }

    public function replicate(AuthUser $authUser, Peripheral $peripheral): bool
    {
        return $authUser->can('Replicate:Peripheral');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Peripheral');
    }

}