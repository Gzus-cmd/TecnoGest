<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeders de TecnoGest...');
        $this->command->newLine();

        // Crear usuarios administrativos
        $this->command->info('👤 Creando usuarios...');
        
        User::firstOrCreate(
            ['email' => 'admin@tecnogest.com'],
            [
                'dni' => '12345678',
                'name' => 'Administrador Principal',
                'position' => 'Administrador de Sistema',
                'phone' => '999888777',
                'is_active' => true,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'soporte@tecnogest.com'],
            [
                'dni' => '87654321',
                'name' => 'Usuario Soporte',
                'position' => 'Técnico de Soporte',
                'phone' => '999888666',
                'is_active' => true,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        $this->command->info('✅ Usuarios creados: admin@tecnogest.com y soporte@tecnogest.com (password: password)');
        $this->command->newLine();

        // Ejecutar seeders en orden lógico
        $this->command->info('📊 Ejecutando seeders de datos maestros...');
        $this->command->newLine();

        $this->call([
            // Datos base del sistema
            LocationSeeder::class,
            ProviderSeeder::class,
            
            // Componentes de hardware
            CPUSeeder::class,
            GPUSeeder::class,
            RAMSeeder::class,
            ROMSeeder::class,
            MotherboardSeeder::class,
            PowerSupplySeeder::class,
            
            // Periféricos
            PeripheralsSeeder::class,
            
            // Sistemas Operativos
            OSSeeder::class,
            
            // Modelos de dispositivos
            PrinterModelSeeder::class,
            ProjectorModelSeeder::class,
            
            // Catálogo de repuestos
            SparePartSeeder::class,
            
            // Instancias de repuestos (componentes)
            SparePartComponentSeeder::class,
            
            // Dispositivos (computadoras, impresoras, proyectores)
            ComputerSeeder::class,
            PrinterSeeder::class,
            ProjectorSeeder::class,
            
            // Historial de mantenimientos y traslados
            MaintenanceSeeder::class,
            TransferSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🎉 ¡Todos los seeders se ejecutaron correctamente!');
        $this->command->info('📌 Base de datos lista para usar.');
    }
}
