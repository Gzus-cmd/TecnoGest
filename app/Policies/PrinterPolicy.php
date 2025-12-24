<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Printer;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrinterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Printer');
    }

    public function view(AuthUser $authUser, Printer $printer): bool
    {
        return $authUser->can('View:Printer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Printer');
    }

    public function update(AuthUser $authUser, Printer $printer): bool
    {
        return $authUser->can('Update:Printer');
    }

    public function delete(AuthUser $authUser, Printer $printer): bool
    {
        return $authUser->can('Delete:Printer');
    }

    public function restore(AuthUser $authUser, Printer $printer): bool
    {
        return $authUser->can('Restore:Printer');
    }

    public function forceDelete(AuthUser $authUser, Printer $printer): bool
    {
        return $authUser->can('ForceDelete:Printer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Printer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Printer');
    }

    public function replicate(AuthUser $authUser, Printer $printer): bool
    {
        return $authUser->can('Replicate:Printer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Printer');
    }

}