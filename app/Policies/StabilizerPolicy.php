<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Stabilizer;
use Illuminate\Auth\Access\HandlesAuthorization;

class StabilizerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Stabilizer');
    }

    public function view(AuthUser $authUser, Stabilizer $stabilizer): bool
    {
        return $authUser->can('View:Stabilizer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Stabilizer');
    }

    public function update(AuthUser $authUser, Stabilizer $stabilizer): bool
    {
        return $authUser->can('Update:Stabilizer');
    }

    public function delete(AuthUser $authUser, Stabilizer $stabilizer): bool
    {
        return $authUser->can('Delete:Stabilizer');
    }

    public function restore(AuthUser $authUser, Stabilizer $stabilizer): bool
    {
        return $authUser->can('Restore:Stabilizer');
    }

    public function forceDelete(AuthUser $authUser, Stabilizer $stabilizer): bool
    {
        return $authUser->can('ForceDelete:Stabilizer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Stabilizer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Stabilizer');
    }

    public function replicate(AuthUser $authUser, Stabilizer $stabilizer): bool
    {
        return $authUser->can('Replicate:Stabilizer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Stabilizer');
    }

}