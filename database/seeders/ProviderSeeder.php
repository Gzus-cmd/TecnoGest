<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'ruc' => '20123456789',
                'name' => 'TechPro Distribuidores SAC',
                'phone' => '+51 1 234-5678',
                'email' => 'ventas@techpro.com.pe',
                'address' => 'Av. Tecnología 123, Lima, Perú',
                'status' => true,
            ],
            [
                'ruc' => '20987654321',
                'name' => 'Importaciones Digitales EIRL',
                'phone' => '+51 1 987-6543',
                'email' => 'contacto@impdigitales.com.pe',
                'address' => 'Jr. Los Inventarios 456, Lima, Perú',
                'status' => true,
            ],
            [
                'ruc' => '20555444333',
                'name' => 'Componentes y Repuestos del Perú SAC',
                'phone' => '+51 1 555-4444',
                'email' => 'info@comprepuestos.pe',
                'address' => 'Av. Industrial 789, Callao, Perú',
                'status' => true,
            ],
            [
                'ruc' => '20666777888',
                'name' => 'Soluciones Empresariales Tech SRL',
                'phone' => '+51 1 666-7777',
                'email' => 'soporte@solemptech.com',
                'address' => 'Calle Innovación 321, San Isidro, Lima',
                'status' => true,
            ],
            [
                'ruc' => '20111222333',
                'name' => 'Proyección y Audio Visual SAC',
                'phone' => '+51 1 111-2222',
                'email' => 'ventas@proyav.pe',
                'address' => 'Av. Multimedia 654, Miraflores, Lima',
                'status' => true,
            ],
            [
                'ruc' => '20999888777',
                'name' => 'Suministros Informáticos del Norte',
                'phone' => '+51 44 999-8888',
                'email' => 'pedidos@sumnorte.pe',
                'address' => 'Jr. Computación 987, Trujillo, Perú',
                'status' => true,
            ],
            [
                'ruc' => '20444333222',
                'name' => 'Global Tech Supplies SA',
                'phone' => '+51 1 444-3333',
                'email' => 'globaltechpe@gmail.com',
                'address' => 'Av. República 147, La Victoria, Lima',
                'status' => false, // Proveedor inactivo de ejemplo
            ],
        ];

        foreach ($providers as $provider) {
            Provider::firstOrCreate(
                ['ruc' => $provider['ruc']], 
                $provider
            );
        }

        $activeCount = Provider::where('status', true)->count();
        $totalCount = Provider::count();
        
        $this->command->info("✅ Proveedores creados: {$activeCount} activos de {$totalCount} total");
    }
}
