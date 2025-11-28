<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrinterModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrinterModelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrinterModel');
    }

    public function view(AuthUser $authUser, PrinterModel $printerModel): bool
    {
        return $authUser->can('View:PrinterModel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrinterModel');
    }

    public function update(AuthUser $authUser, PrinterModel $printerModel): bool
    {
        return $authUser->can('Update:PrinterModel');
    }

    public function delete(AuthUser $authUser, PrinterModel $printerModel): bool
    {
        return $authUser->can('Delete:PrinterModel');
    }

    public function restore(AuthUser $authUser, PrinterModel $printerModel): bool
    {
        return $authUser->can('Restore:PrinterModel');
    }

    public function forceDelete(AuthUser $authUser, PrinterModel $printerModel): bool
    {
        return $authUser->can('ForceDelete:PrinterModel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrinterModel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrinterModel');
    }

    public function replicate(AuthUser $authUser, PrinterModel $printerModel): bool
    {
        return $authUser->can('Replicate:PrinterModel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrinterModel');
    }

}