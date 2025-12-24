<?php

namespace Database\Seeders;

use App\Models\NetworkAdapter;
use App\Models\Monitor;
use App\Models\Keyboard;
use App\Models\Mouse;
use App\Models\AudioDevice;
use App\Models\Splitter;
use App\Models\Stabilizer;
use App\Models\TowerCase;
use App\Models\Component;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class PeripheralsSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Operativo', 'Deficiente', 'Retirado'];
        $provider = Provider::first();

        // Network Adapters - schema: brand, model, type, speed (float), interface, watts
        $this->createPeripherals([
            ['brand' => 'Intel', 'model' => 'I225-V', 'type' => '2.5G', 'speed' => 2.5, 'interface' => 'PCIe 3.0', 'watts' => 3],
            ['brand' => 'Realtek', 'model' => 'RTL8125B', 'type' => '2.5G', 'speed' => 2.5, 'interface' => 'PCIe 3.0', 'watts' => 2],
            ['brand' => 'ASUS', 'model' => 'ProArt PAX12', 'type' => '10G', 'speed' => 10.0, 'interface' => 'PCIe 4.0', 'watts' => 5],
            ['brand' => 'Broadcom', 'model' => 'BCM5725', 'type' => '1G', 'speed' => 1.0, 'interface' => 'PCIe', 'watts' => 2],
            ['brand' => 'Marvell', 'model' => 'AQC107', 'type' => '5G', 'speed' => 5.0, 'interface' => 'PCIe 3.0', 'watts' => 4],
            ['brand' => 'Intel', 'model' => 'X550-T1', 'type' => '10G', 'speed' => 10.0, 'interface' => 'PCIe 3.0', 'watts' => 6],
        ], NetworkAdapter::class, 'NIC', $statuses, $provider);

        // Tower Cases - schema: brand, model, format
        $this->createPeripherals([
            ['brand' => 'Corsair', 'model' => 'Obsidian 1000D', 'format' => 'ATX'],
            ['brand' => 'NZXT', 'model' => 'H7 Flow', 'format' => 'ATX'],
            ['brand' => 'Fractal Design', 'model' => 'Torrent RGB', 'format' => 'ATX'],
            ['brand' => 'Lian Li', 'model' => 'O11 Dynamic', 'format' => 'ATX'],
            ['brand' => 'Thermaltake', 'model' => 'Core X71', 'format' => 'ATX'],
            ['brand' => 'Cougar', 'model' => 'Panzer EVO', 'format' => 'ATX'],
        ], TowerCase::class, 'CASE', $statuses, $provider);

        // Monitors - schema: brand, model, size (float), resolution, vga (boolean), hdmi (boolean)
        $this->createPeripherals([
            ['brand' => 'Dell', 'model' => 'UltraSharp U2723DE', 'size' => 27.0, 'resolution' => '2560x1440', 'vga' => true, 'hdmi' => true],
            ['brand' => 'LG', 'model' => '27UP550', 'size' => 27.0, 'resolution' => '3840x2160', 'vga' => true, 'hdmi' => true],
            ['brand' => 'ASUS', 'model' => 'PA279CV', 'size' => 27.0, 'resolution' => '2560x1440', 'vga' => false, 'hdmi' => true],
            ['brand' => 'BenQ', 'model' => 'EW3270U', 'size' => 32.0, 'resolution' => '3840x2160', 'vga' => true, 'hdmi' => true],
            ['brand' => 'Samsung', 'model' => 'U32J590UQ', 'size' => 32.0, 'resolution' => '3840x2160', 'vga' => true, 'hdmi' => true],
            ['brand' => 'AOC', 'model' => 'U2790PQU', 'size' => 27.0, 'resolution' => '3840x2160', 'vga' => true, 'hdmi' => true],
        ], Monitor::class, 'MON', $statuses, $provider);

        // Keyboards - schema: brand, model, connection, language
        $this->createPeripherals([
            ['brand' => 'Corsair', 'model' => 'K95 Platinum', 'connection' => 'Wireless', 'language' => 'Español'],
            ['brand' => 'Logitech', 'model' => 'MX Mechanical', 'connection' => 'Wireless', 'language' => 'Español'],
            ['brand' => 'Keychron', 'model' => 'Q1', 'connection' => 'Wired', 'language' => 'Español'],
            ['brand' => 'Ducky', 'model' => 'One 3', 'connection' => 'Wired', 'language' => 'Español'],
            ['brand' => 'Razer', 'model' => 'DeathStalker Pro', 'connection' => 'Wireless', 'language' => 'Español'],
            ['brand' => 'SteelSeries', 'model' => 'Apex Pro', 'connection' => 'Wired', 'language' => 'Español'],
        ], Keyboard::class, 'KB', $statuses, $provider);

        // Mice - schema: brand, model, connection
        $this->createPeripherals([
            ['brand' => 'Logitech', 'model' => 'MX Master 3S', 'connection' => 'Wireless'],
            ['brand' => 'Corsair', 'model' => 'M65 RGB Ultra', 'connection' => 'Wired'],
            ['brand' => 'Razer', 'model' => 'DeathAdder V3', 'connection' => 'Wired'],
            ['brand' => 'SteelSeries', 'model' => 'Prime Pro', 'connection' => 'Wired'],
            ['brand' => 'BenQ', 'model' => 'EC2', 'connection' => 'Wired'],
            ['brand' => 'Microsoft', 'model' => 'Designer Mouse', 'connection' => 'Wireless'],
        ], Mouse::class, 'MOUSE', $statuses, $provider);

        // Audio Devices - schema: brand, model, type, speakers (integer)
        $this->createPeripherals([
            ['brand' => 'Yamaha', 'model' => 'HS8', 'type' => 'Altavoz', 'speakers' => 2],
            ['brand' => 'Focal', 'model' => 'Alpha 65', 'type' => 'Altavoz', 'speakers' => 2],
            ['brand' => 'Audio-Technica', 'model' => 'AT2050', 'type' => 'Micrófono', 'speakers' => 1],
            ['brand' => 'Shure', 'model' => 'SM7B', 'type' => 'Micrófono', 'speakers' => 1],
            ['brand' => 'AKG', 'model' => 'Pro Audio K141', 'type' => 'Auriculares', 'speakers' => 2],
            ['brand' => 'Sennheiser', 'model' => 'HD 660S2', 'type' => 'Auriculares', 'speakers' => 2],
        ], AudioDevice::class, 'AUDIO', $statuses, $provider);

        // Splitters - schema: brand, model, ports (integer), frequency (string)
        $this->createPeripherals([
            ['brand' => 'Startech', 'model' => 'VSC4HDMI', 'ports' => 4, 'frequency' => 'HDMI'],
            ['brand' => 'ATEN', 'model' => 'VS0801H', 'ports' => 8, 'frequency' => 'HDMI'],
            ['brand' => 'Extron', 'model' => 'Crosspoint', 'ports' => 16, 'frequency' => 'HDMI'],
            ['brand' => 'Datapath', 'model' => 'Fx4', 'ports' => 4, 'frequency' => 'DisplayPort'],
            ['brand' => 'Kramer', 'model' => 'VP-401X', 'ports' => 4, 'frequency' => 'VGA'],
            ['brand' => 'PNY', 'model' => 'VCQK1200DPB', 'ports' => 1, 'frequency' => 'HDMI'],
        ], Splitter::class, 'SPLIT', $statuses, $provider);

        // Stabilizers - schema: brand, model, outlets (integer), watts (integer)
        $this->createPeripherals([
            ['brand' => 'APC', 'model' => 'AVR 1000', 'outlets' => 8, 'watts' => 1000],
            ['brand' => 'Tripp Lite', 'model' => 'LC1800', 'outlets' => 8, 'watts' => 1800],
            ['brand' => 'Tecnosystem', 'model' => 'TS1000', 'outlets' => 8, 'watts' => 1000],
            ['brand' => 'Practica', 'model' => 'PR-2000', 'outlets' => 8, 'watts' => 2000],
            ['brand' => 'Schneider Electric', 'model' => 'Easy UPS', 'outlets' => 8, 'watts' => 1500],
            ['brand' => 'Riello', 'model' => 'Multiplug', 'outlets' => 6, 'watts' => 1200],
        ], Stabilizer::class, 'STAB', $statuses, $provider);

        $this->command->info('✅ Periféricos y componentes varios creados correctamente.');
    }

    private function createPeripherals($items, $modelClass, $prefix, $statuses, $provider)
    {
        foreach ($items as $itemData) {
            $item = $modelClass::firstOrCreate($itemData);
            
            // Crear 3 componentes por periférico
            for ($i = 0; $i < 3; $i++) {
                // 60% Operativo, 20% Deficiente, 20% Retirado
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    $status = 'Operativo';
                } elseif ($rand <= 80) {
                    $status = 'Deficiente';
                } else {
                    $status = 'Retirado';
                }
                
                $inputDate = now()->subMonths(rand(1, 24));

                Component::create([
                    'componentable_type' => $modelClass,
                    'componentable_id' => $item->id,
                    'serial' => $prefix . '-' . strtoupper(bin2hex(random_bytes(5))),
                    'input_date' => $inputDate->toDateString(),
                    'output_date' => $status === 'Retirado' ? $inputDate->addMonths(rand(6, 18))->toDateString() : null,
                    'status' => $status,
                    'warranty_months' => rand(12, 60),
                    'provider_id' => $provider->id,
                ]);
            }
        }
    }
}
