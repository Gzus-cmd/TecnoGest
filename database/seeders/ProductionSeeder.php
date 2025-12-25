<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder de PRODUCCIÓN - Solo carga la configuración inicial mínima
 *
 * Uso: php artisan db:seed --class=ProductionSeeder
 *
 * Incluye:
 * - Usuarios administrativos básicos
 * - Roles y permisos del sistema
 * - Ubicaciones base (solo taller)
 *
 * NO incluye datos de prueba (computadoras, componentes, etc.)
 */
class ProductionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║           🏭 SEEDER DE PRODUCCIÓN - TecnoGest                ║');
        $this->command->info('║                 Configuración Inicial Mínima                 ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════════
        // 1. USUARIOS ADMINISTRATIVOS
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('👤 Creando usuarios administrativos...');

        $admin = User::firstOrCreate(
            ['email' => 'admin@tecnogest.com'],
            [
                'dni' => '00000001',
                'name' => 'Administrador',
                'phone' => '000000000',
                'is_active' => true,
                'password' => Hash::make('admin123'),
            ]
        );
        $this->command->info("   ✓ Admin: admin@tecnogest.com (contraseña: admin123)");

        $tecnico = User::firstOrCreate(
            ['email' => 'tecnico@tecnogest.com'],
            [
                'dni' => '00000002',
                'name' => 'Técnico de Soporte',
                'phone' => '000000001',
                'is_active' => true,
                'password' => Hash::make('tecnico123'),
            ]
        );
        $this->command->info("   ✓ Técnico: tecnico@tecnogest.com (contraseña: tecnico123)");

        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════════
        // 2. ROLES Y PERMISOS
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('🔐 Configurando roles y permisos...');
        $this->call(RoleAndPermissionSeeder::class);
        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════════
        // 3. ASIGNAR ROLES A USUARIOS
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('🔗 Asignando roles a usuarios...');

        // Recargar usuarios para evitar problemas de cache
        $admin->refresh();
        $tecnico->refresh();

        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
            $this->command->info("   ✓ Rol 'super_admin' asignado a admin@tecnogest.com");
        }

        if (!$tecnico->hasRole('tecnico')) {
            $tecnico->assignRole('tecnico');
            $this->command->info("   ✓ Rol 'tecnico' asignado a tecnico@tecnogest.com");
        }

        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════════
        // 4. UBICACIÓN BASE (TALLER)
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('📍 Creando ubicación de taller...');

        \App\Models\Location::firstOrCreate(
            ['name' => 'Taller de Informática'],
            [
                'pavilion' => 'Principal',
                'apartment' => 1,
                'is_workshop' => true,
            ]
        );
        $this->command->info("   ✓ Taller de Informática creado (is_workshop = true)");

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║    ✅ CONFIGURACIÓN INICIAL COMPLETADA                       ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║                                                              ║');
        $this->command->info('║  📋 PRÓXIMOS PASOS:                                          ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  1. Ingresar a: http://localhost/admin                       ║');
        $this->command->info('║  2. Usar: admin@tecnogest.com / admin123                     ║');
        $this->command->info('║  3. Configurar ubicaciones adicionales                       ║');
        $this->command->info('║  4. Configurar proveedores                                   ║');
        $this->command->info('║  5. Registrar componentes y dispositivos                     ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  ⚠️  IMPORTANTE: Cambiar contraseñas en producción           ║');
        $this->command->info('║                                                              ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->newLine();
    }
}
