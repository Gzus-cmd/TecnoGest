<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GPU;
use Illuminate\Auth\Access\HandlesAuthorization;

class GPUPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GPU');
    }

    public function view(AuthUser $authUser, GPU $gPU): bool
    {
        return $authUser->can('View:GPU');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GPU');
    }

    public function update(AuthUser $authUser, GPU $gPU): bool
    {
        return $authUser->can('Update:GPU');
    }

    public function delete(AuthUser $authUser, GPU $gPU): bool
    {
        return $authUser->can('Delete:GPU');
    }

    public function restore(AuthUser $authUser, GPU $gPU): bool
    {
        return $authUser->can('Restore:GPU');
    }

    public function forceDelete(AuthUser $authUser, GPU $gPU): bool
    {
        return $authUser->can('ForceDelete:GPU');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GPU');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GPU');
    }

    public function replicate(AuthUser $authUser, GPU $gPU): bool
    {
        return $authUser->can('Replicate:GPU');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GPU');
    }

}