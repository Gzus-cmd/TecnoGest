<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PowerSupply;
use Illuminate\Auth\Access\HandlesAuthorization;

class PowerSupplyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PowerSupply');
    }

    public function view(AuthUser $authUser, PowerSupply $powerSupply): bool
    {
        return $authUser->can('View:PowerSupply');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PowerSupply');
    }

    public function update(AuthUser $authUser, PowerSupply $powerSupply): bool
    {
        return $authUser->can('Update:PowerSupply');
    }

    public function delete(AuthUser $authUser, PowerSupply $powerSupply): bool
    {
        return $authUser->can('Delete:PowerSupply');
    }

    public function restore(AuthUser $authUser, PowerSupply $powerSupply): bool
    {
        return $authUser->can('Restore:PowerSupply');
    }

    public function forceDelete(AuthUser $authUser, PowerSupply $powerSupply): bool
    {
        return $authUser->can('ForceDelete:PowerSupply');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PowerSupply');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PowerSupply');
    }

    public function replicate(AuthUser $authUser, PowerSupply $powerSupply): bool
    {
        return $authUser->can('Replicate:PowerSupply');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PowerSupply');
    }

}