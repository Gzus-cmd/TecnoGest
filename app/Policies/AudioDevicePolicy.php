<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AudioDevice;
use Illuminate\Auth\Access\HandlesAuthorization;

class AudioDevicePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AudioDevice');
    }

    public function view(AuthUser $authUser, AudioDevice $audioDevice): bool
    {
        return $authUser->can('View:AudioDevice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AudioDevice');
    }

    public function update(AuthUser $authUser, AudioDevice $audioDevice): bool
    {
        return $authUser->can('Update:AudioDevice');
    }

    public function delete(AuthUser $authUser, AudioDevice $audioDevice): bool
    {
        return $authUser->can('Delete:AudioDevice');
    }

    public function restore(AuthUser $authUser, AudioDevice $audioDevice): bool
    {
        return $authUser->can('Restore:AudioDevice');
    }

    public function forceDelete(AuthUser $authUser, AudioDevice $audioDevice): bool
    {
        return $authUser->can('ForceDelete:AudioDevice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AudioDevice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AudioDevice');
    }

    public function replicate(AuthUser $authUser, AudioDevice $audioDevice): bool
    {
        return $authUser->can('Replicate:AudioDevice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AudioDevice');
    }

}