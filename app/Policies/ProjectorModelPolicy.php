<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProjectorModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectorModelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectorModel');
    }

    public function view(AuthUser $authUser, ProjectorModel $projectorModel): bool
    {
        return $authUser->can('View:ProjectorModel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectorModel');
    }

    public function update(AuthUser $authUser, ProjectorModel $projectorModel): bool
    {
        return $authUser->can('Update:ProjectorModel');
    }

    public function delete(AuthUser $authUser, ProjectorModel $projectorModel): bool
    {
        return $authUser->can('Delete:ProjectorModel');
    }

    public function restore(AuthUser $authUser, ProjectorModel $projectorModel): bool
    {
        return $authUser->can('Restore:ProjectorModel');
    }

    public function forceDelete(AuthUser $authUser, ProjectorModel $projectorModel): bool
    {
        return $authUser->can('ForceDelete:ProjectorModel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectorModel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectorModel');
    }

    public function replicate(AuthUser $authUser, ProjectorModel $projectorModel): bool
    {
        return $authUser->can('Replicate:ProjectorModel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectorModel');
    }

}