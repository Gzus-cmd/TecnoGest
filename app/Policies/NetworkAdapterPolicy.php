<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NetworkAdapter;
use Illuminate\Auth\Access\HandlesAuthorization;

class NetworkAdapterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NetworkAdapter');
    }

    public function view(AuthUser $authUser, NetworkAdapter $networkAdapter): bool
    {
        return $authUser->can('View:NetworkAdapter');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NetworkAdapter');
    }

    public function update(AuthUser $authUser, NetworkAdapter $networkAdapter): bool
    {
        return $authUser->can('Update:NetworkAdapter');
    }

    public function delete(AuthUser $authUser, NetworkAdapter $networkAdapter): bool
    {
        return $authUser->can('Delete:NetworkAdapter');
    }

    public function restore(AuthUser $authUser, NetworkAdapter $networkAdapter): bool
    {
        return $authUser->can('Restore:NetworkAdapter');
    }

    public function forceDelete(AuthUser $authUser, NetworkAdapter $networkAdapter): bool
    {
        return $authUser->can('ForceDelete:NetworkAdapter');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NetworkAdapter');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NetworkAdapter');
    }

    public function replicate(AuthUser $authUser, NetworkAdapter $networkAdapter): bool
    {
        return $authUser->can('Replicate:NetworkAdapter');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NetworkAdapter');
    }

}