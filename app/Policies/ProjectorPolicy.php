<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Projector;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Projector');
    }

    public function view(AuthUser $authUser, Projector $projector): bool
    {
        return $authUser->can('View:Projector');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Projector');
    }

    public function update(AuthUser $authUser, Projector $projector): bool
    {
        return $authUser->can('Update:Projector');
    }

    public function delete(AuthUser $authUser, Projector $projector): bool
    {
        return $authUser->can('Delete:Projector');
    }

    public function restore(AuthUser $authUser, Projector $projector): bool
    {
        return $authUser->can('Restore:Projector');
    }

    public function forceDelete(AuthUser $authUser, Projector $projector): bool
    {
        return $authUser->can('ForceDelete:Projector');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Projector');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Projector');
    }

    public function replicate(AuthUser $authUser, Projector $projector): bool
    {
        return $authUser->can('Replicate:Projector');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Projector');
    }

}