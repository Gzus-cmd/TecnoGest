<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TowerCase;
use Illuminate\Auth\Access\HandlesAuthorization;

class TowerCasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TowerCase');
    }

    public function view(AuthUser $authUser, TowerCase $towerCase): bool
    {
        return $authUser->can('View:TowerCase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TowerCase');
    }

    public function update(AuthUser $authUser, TowerCase $towerCase): bool
    {
        return $authUser->can('Update:TowerCase');
    }

    public function delete(AuthUser $authUser, TowerCase $towerCase): bool
    {
        return $authUser->can('Delete:TowerCase');
    }

    public function restore(AuthUser $authUser, TowerCase $towerCase): bool
    {
        return $authUser->can('Restore:TowerCase');
    }

    public function forceDelete(AuthUser $authUser, TowerCase $towerCase): bool
    {
        return $authUser->can('ForceDelete:TowerCase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TowerCase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TowerCase');
    }

    public function replicate(AuthUser $authUser, TowerCase $towerCase): bool
    {
        return $authUser->can('Replicate:TowerCase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TowerCase');
    }

}