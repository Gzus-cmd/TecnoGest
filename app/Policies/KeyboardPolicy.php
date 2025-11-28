<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Keyboard;
use Illuminate\Auth\Access\HandlesAuthorization;

class KeyboardPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Keyboard');
    }

    public function view(AuthUser $authUser, Keyboard $keyboard): bool
    {
        return $authUser->can('View:Keyboard');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Keyboard');
    }

    public function update(AuthUser $authUser, Keyboard $keyboard): bool
    {
        return $authUser->can('Update:Keyboard');
    }

    public function delete(AuthUser $authUser, Keyboard $keyboard): bool
    {
        return $authUser->can('Delete:Keyboard');
    }

    public function restore(AuthUser $authUser, Keyboard $keyboard): bool
    {
        return $authUser->can('Restore:Keyboard');
    }

    public function forceDelete(AuthUser $authUser, Keyboard $keyboard): bool
    {
        return $authUser->can('ForceDelete:Keyboard');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Keyboard');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Keyboard');
    }

    public function replicate(AuthUser $authUser, Keyboard $keyboard): bool
    {
        return $authUser->can('Replicate:Keyboard');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Keyboard');
    }

}