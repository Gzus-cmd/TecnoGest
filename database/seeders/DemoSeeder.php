<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder de DESARROLLO/DEMO - Carga todos los datos de prueba
 *
 * Uso: php artisan db:seed --class=DemoSeeder
 *   o: php artisan db:seed (por defecto)
 *
 * Incluye:
 * - Todo lo del ProductionSeeder
 * - Ubicaciones de prueba
 * - Proveedores de ejemplo
 * - Componentes de hardware (CPU, GPU, RAM, etc.)
 * - Periféricos
 * - Sistemas operativos
 * - Modelos de impresoras y proyectores
 * - Dispositivos (computadoras, impresoras, proyectores)
 * - Historial de mantenimientos y traslados
 */
class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║            🎮 SEEDER DE DEMO - TecnoGest                     ║');
        $this->command->info('║              Datos Completos de Prueba                       ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════════
        // 1. USUARIOS DE PRUEBA
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('👤 Creando usuarios de prueba...');

        User::firstOrCreate(
            ['email' => 'admin@tecnogest.com'],
            [
                'dni' => '12345678',
                'name' => 'Administrador Principal',
                'phone' => '999888777',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
        $this->command->info("   ✓ admin@tecnogest.com (password: password)");

        User::firstOrCreate(
            ['email' => 'soporte@tecnogest.com'],
            [
                'dni' => '87654321',
                'name' => 'Usuario Soporte',
                'phone' => '999888666',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
        $this->command->info("   ✓ soporte@tecnogest.com (password: password)");

        User::firstOrCreate(
            ['email' => 'viewer@tecnogest.com'],
            [
                'dni' => '11111111',
                'name' => 'Usuario Visualizador',
                'phone' => '999888555',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
        $this->command->info("   ✓ viewer@tecnogest.com (password: password)");

        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════════
        // 2. ROLES Y PERMISOS
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('🔐 Configurando roles y permisos...');
        $this->call(RoleAndPermissionSeeder::class);

        // Generar permisos de Filament Shield
        $this->call(ShieldSeeder::class);

        // Permisos personalizados para acciones específicas
        $this->call(CustomPermissionsSeeder::class);

        // Asignar roles a usuarios de demo
        $adminUser = User::where('email', 'admin@tecnogest.com')->first();
        $supportUser = User::where('email', 'soporte@tecnogest.com')->first();
        $viewerUser = User::where('email', 'viewer@tecnogest.com')->first();

        if ($adminUser && !$adminUser->hasRole('super_admin')) {
            $adminUser->assignRole('super_admin');
        }
        if ($supportUser && !$supportUser->hasRole('tecnico')) {
            $supportUser->assignRole('tecnico');
        }
        if ($viewerUser && !$viewerUser->hasRole('viewer')) {
            $viewerUser->assignRole('viewer');
        }

        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════════
        // 3. DATOS MAESTROS
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('📊 Cargando datos maestros...');
        $this->command->newLine();

        $this->call([
            // Ubicaciones y proveedores
            LocationSeeder::class,
            ProviderSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('💾 Cargando componentes de hardware...');
        $this->command->newLine();

        $this->call([
            // Componentes de hardware
            CPUSeeder::class,
            GPUSeeder::class,
            RAMSeeder::class,
            ROMSeeder::class,
            MotherboardSeeder::class,
            PowerSupplySeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🖥️ Cargando periféricos y sistemas...');
        $this->command->newLine();

        $this->call([
            // Periféricos
            PeripheralsSeeder::class,

            // Sistemas Operativos
            OSSeeder::class,

            // Modelos de dispositivos
            PrinterModelSeeder::class,
            ProjectorModelSeeder::class,

            // Catálogo de repuestos
            SparePartSeeder::class,
            SparePartComponentSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🏢 Cargando dispositivos...');
        $this->command->newLine();

        $this->call([
            // Dispositivos
            ComputerSeeder::class,
            PrinterSeeder::class,
            ProjectorSeeder::class,

            // Componentes para impresoras y proyectores
            PrinterProjectorComponentsSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('📝 Cargando historial de operaciones...');
        $this->command->newLine();

        $this->call([
            // Historial
            MaintenanceSeeder::class,
            TransferSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║    ✅ DATOS DE DEMO CARGADOS COMPLETAMENTE                   ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║                                                              ║');
        $this->command->info('║  🔑 CREDENCIALES DE ACCESO:                                  ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Super Admin: admin@tecnogest.com / password                 ║');
        $this->command->info('║  Técnico:     soporte@tecnogest.com / password               ║');
        $this->command->info('║  Viewer:      viewer@tecnogest.com / password                ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  📍 URL: http://localhost/admin                              ║');
        $this->command->info('║                                                              ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->newLine();
    }
}
