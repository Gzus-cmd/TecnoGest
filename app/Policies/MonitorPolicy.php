<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Monitor;
use Illuminate\Auth\Access\HandlesAuthorization;

class MonitorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Monitor');
    }

    public function view(AuthUser $authUser, Monitor $monitor): bool
    {
        return $authUser->can('View:Monitor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Monitor');
    }

    public function update(AuthUser $authUser, Monitor $monitor): bool
    {
        return $authUser->can('Update:Monitor');
    }

    public function delete(AuthUser $authUser, Monitor $monitor): bool
    {
        return $authUser->can('Delete:Monitor');
    }

    public function restore(AuthUser $authUser, Monitor $monitor): bool
    {
        return $authUser->can('Restore:Monitor');
    }

    public function forceDelete(AuthUser $authUser, Monitor $monitor): bool
    {
        return $authUser->can('ForceDelete:Monitor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Monitor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Monitor');
    }

    public function replicate(AuthUser $authUser, Monitor $monitor): bool
    {
        return $authUser->can('Replicate:Monitor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Monitor');
    }

}