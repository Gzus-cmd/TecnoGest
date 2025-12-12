<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Splitter;
use Illuminate\Auth\Access\HandlesAuthorization;

class SplitterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Splitter');
    }

    public function view(AuthUser $authUser, Splitter $splitter): bool
    {
        return $authUser->can('View:Splitter');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Splitter');
    }

    public function update(AuthUser $authUser, Splitter $splitter): bool
    {
        return $authUser->can('Update:Splitter');
    }

    public function delete(AuthUser $authUser, Splitter $splitter): bool
    {
        return $authUser->can('Delete:Splitter');
    }

    public function restore(AuthUser $authUser, Splitter $splitter): bool
    {
        return $authUser->can('Restore:Splitter');
    }

    public function forceDelete(AuthUser $authUser, Splitter $splitter): bool
    {
        return $authUser->can('ForceDelete:Splitter');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Splitter');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Splitter');
    }

    public function replicate(AuthUser $authUser, Splitter $splitter): bool
    {
        return $authUser->can('Replicate:Splitter');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Splitter');
    }

}