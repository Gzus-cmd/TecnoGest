<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CPU;
use Illuminate\Auth\Access\HandlesAuthorization;

class CPUPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CPU');
    }

    public function view(AuthUser $authUser, CPU $cPU): bool
    {
        return $authUser->can('View:CPU');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CPU');
    }

    public function update(AuthUser $authUser, CPU $cPU): bool
    {
        return $authUser->can('Update:CPU');
    }

    public function delete(AuthUser $authUser, CPU $cPU): bool
    {
        return $authUser->can('Delete:CPU');
    }

    public function restore(AuthUser $authUser, CPU $cPU): bool
    {
        return $authUser->can('Restore:CPU');
    }

    public function forceDelete(AuthUser $authUser, CPU $cPU): bool
    {
        return $authUser->can('ForceDelete:CPU');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CPU');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CPU');
    }

    public function replicate(AuthUser $authUser, CPU $cPU): bool
    {
        return $authUser->can('Replicate:CPU');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CPU');
    }

}