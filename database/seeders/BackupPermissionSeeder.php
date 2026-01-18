<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BackupPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el permiso para la página de backup
        $permission = Permission::firstOrCreate(
            ['name' => 'page_BackupDatabase'],
            ['guard_name' => 'web']
        );

        // Asignar el permiso al rol super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin && !$superAdmin->hasPermissionTo('page_BackupDatabase')) {
            $superAdmin->givePermissionTo('page_BackupDatabase');
        }

        echo "✅ Permiso page_BackupDatabase creado y asignado a super_admin\n";
    }
}
