<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Motherboard;
use Illuminate\Auth\Access\HandlesAuthorization;

class MotherboardPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Motherboard');
    }

    public function view(AuthUser $authUser, Motherboard $motherboard): bool
    {
        return $authUser->can('View:Motherboard');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Motherboard');
    }

    public function update(AuthUser $authUser, Motherboard $motherboard): bool
    {
        return $authUser->can('Update:Motherboard');
    }

    public function delete(AuthUser $authUser, Motherboard $motherboard): bool
    {
        return $authUser->can('Delete:Motherboard');
    }

    public function restore(AuthUser $authUser, Motherboard $motherboard): bool
    {
        return $authUser->can('Restore:Motherboard');
    }

    public function forceDelete(AuthUser $authUser, Motherboard $motherboard): bool
    {
        return $authUser->can('ForceDelete:Motherboard');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Motherboard');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Motherboard');
    }

    public function replicate(AuthUser $authUser, Motherboard $motherboard): bool
    {
        return $authUser->can('Replicate:Motherboard');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Motherboard');
    }

}