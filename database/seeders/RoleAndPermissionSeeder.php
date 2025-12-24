<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Configurando roles y permisos...');
        $this->command->newLine();

        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===================================================================
        // 1. CREAR PERMISOS
        // ===================================================================
        // Los permisos se crean automáticamente con el comando:
        // php artisan shield:generate
        // Pero aquí creamos algunos permisos personalizados si son necesarios

        $this->command->info('📋 Creando permisos personalizados...');
        
        // Ejemplo de permisos personalizados (opcional)
        $customPermissions = [
            'view_advanced_reports',
            'export_data',
            'manage_system_settings',
        ];

        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ===================================================================
        // 2. CREAR ROLES
        // ===================================================================
        $this->command->info('👥 Creando roles del sistema...');

        // ROL: Super Administrador
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);
        $this->command->info('✓ Rol creado: Super Admin (acceso total)');

        // ROL: Panel User (usuario básico de panel)
        $panelUser = Role::firstOrCreate([
            'name' => 'panel_user',
            'guard_name' => 'web'
        ]);
        $this->command->info('✓ Rol creado: Panel User (acceso básico)');

        // ROL: Administrador (con permisos específicos)
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);
        $this->command->info('✓ Rol creado: Admin');

        // ROL: Técnico (acceso medio)
        $tecnico = Role::firstOrCreate([
            'name' => 'tecnico',
            'guard_name' => 'web'
        ]);
        $this->command->info('✓ Rol creado: Técnico');

        // ROL: Visualizador (solo lectura)
        $viewer = Role::firstOrCreate([
            'name' => 'viewer',
            'guard_name' => 'web'
        ]);
        $this->command->info('✓ Rol creado: Viewer (solo lectura)');

        // ===================================================================
        // 3. ASIGNAR PERMISOS A ROLES
        // ===================================================================
        $this->command->newLine();
        $this->command->info('🔗 Asignando permisos a roles...');

        // SUPER ADMIN: Obtiene TODOS los permisos automáticamente
        // (esto está configurado en config/filament-shield.php)
        // No necesita asignación manual

        // ADMIN: Permisos completos excepto gestión de roles
        if (Permission::count() > 0) {
            $adminPermissions = Permission::where('name', 'not like', '%role%')
                ->pluck('name')
                ->toArray();
            $admin->syncPermissions($adminPermissions);
            $this->command->info('✓ Permisos asignados al rol Admin');
        }

        // TÉCNICO: Permisos de lectura y escritura en recursos principales
        $tecnicoPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%computer%')
                  ->orWhere('name', 'like', '%printer%')
                  ->orWhere('name', 'like', '%projector%')
                  ->orWhere('name', 'like', '%component%')
                  ->orWhere('name', 'like', '%maintenance%')
                  ->orWhere('name', 'like', '%transfer%');
        })->where('name', 'not like', '%delete%')
          ->pluck('name')
          ->toArray();
        
        if (count($tecnicoPermissions) > 0) {
            $tecnico->syncPermissions($tecnicoPermissions);
            $this->command->info('✓ Permisos asignados al rol Técnico');
        }

        // VIEWER: Solo permisos de visualización
        $viewerPermissions = Permission::where('name', 'like', '%view%')
            ->pluck('name')
            ->toArray();
        
        if (count($viewerPermissions) > 0) {
            $viewer->syncPermissions($viewerPermissions);
            $this->command->info('✓ Permisos asignados al rol Viewer');
        }

        // ===================================================================
        // 4. ASIGNAR ROLES A USUARIOS
        // ===================================================================
        $this->command->newLine();
        $this->command->info('👤 Asignando roles a usuarios...');

        // Asignar Super Admin al usuario administrador
        $adminUser = User::where('email', 'admin@tecnogest.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
            $this->command->info('✓ Rol Super Admin asignado a admin@tecnogest.com');
        }

        // Asignar Técnico al usuario de soporte
        $supportUser = User::where('email', 'soporte@tecnogest.com')->first();
        if ($supportUser) {
            $supportUser->assignRole('tecnico');
            $this->command->info('✓ Rol Técnico asignado a soporte@tecnogest.com');
        }

        $this->command->newLine();
        $this->command->info('✅ Configuración de roles y permisos completada!');
        $this->command->newLine();
        
        // Información adicional
        $this->command->warn('📌 IMPORTANTE:');
        $this->command->line('   - Ejecuta "php artisan shield:generate" para generar todos los permisos de recursos');
        $this->command->line('   - Los permisos se pueden gestionar desde el panel de Filament en Shield > Roles');
        $this->command->line('   - Para asignar roles a nuevos usuarios, usa: $user->assignRole(\'nombre_rol\')');
    }
}
