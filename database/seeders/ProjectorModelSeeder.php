<?php

namespace Database\Seeders;

use App\Models\ProjectorModel;
use Illuminate\Database\Seeder;

class ProjectorModelSeeder extends Seeder
{
    public function run(): void
    {
        $projectorModels = [
            ['brand' => 'Epson', 'model' => 'EB-2250U', 'resolution' => '1920x1200', 'lumens' => 5000, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Sony', 'model' => 'VPL-FHZ75', 'resolution' => '1920x1200', 'lumens' => 6000, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Panasonic', 'model' => 'PT-RZ870', 'resolution' => '1920x1200', 'lumens' => 7000, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Christie', 'model' => 'DHD850', 'resolution' => '2560x1440', 'lumens' => 8500, 'vga' => false, 'hdmi' => true],
            ['brand' => 'Barco', 'model' => 'F50', 'resolution' => '1920x1080', 'lumens' => 5000, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Optoma', 'model' => 'EH412', 'resolution' => '1920x1080', 'lumens' => 4500, 'vga' => true, 'hdmi' => true],
            ['brand' => 'BenQ', 'model' => 'MH534A', 'resolution' => '1920x1080', 'lumens' => 3600, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Casio', 'model' => 'XJ-F10X', 'resolution' => '1024x768', 'lumens' => 3000, 'vga' => true, 'hdmi' => false],
            ['brand' => 'Acer', 'model' => 'P5630', 'resolution' => '1920x1080', 'lumens' => 4500, 'vga' => true, 'hdmi' => true],
            ['brand' => 'ASUS', 'model' => 'PA148CTC', 'resolution' => '1920x1080', 'lumens' => 250, 'vga' => false, 'hdmi' => true],
            ['brand' => 'InFocus', 'model' => 'IN119HDx', 'resolution' => '1920x1080', 'lumens' => 3800, 'vga' => true, 'hdmi' => true],
            ['brand' => 'NEC', 'model' => 'M403H', 'resolution' => '1920x1200', 'lumens' => 4200, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Mitsubishi', 'model' => 'XD8100', 'resolution' => '1920x1440', 'lumens' => 5500, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Sanyo', 'model' => 'PLV-Z2000', 'resolution' => '1920x1080', 'lumens' => 2000, 'vga' => true, 'hdmi' => false],
            ['brand' => 'Yaber', 'model' => 'Y60', 'resolution' => '1280x720', 'lumens' => 6000, 'vga' => false, 'hdmi' => true],
            ['brand' => 'VANKYO', 'model' => 'Leisure 3W', 'resolution' => '800x600', 'lumens' => 3600, 'vga' => false, 'hdmi' => true],
            ['brand' => 'Anker', 'model' => 'Nebula Cosmos', 'resolution' => '1920x1080', 'lumens' => 1200, 'vga' => false, 'hdmi' => false],
            ['brand' => 'Philips', 'model' => 'NeoPix Ultra 2', 'resolution' => '1920x1080', 'lumens' => 1000, 'vga' => false, 'hdmi' => true],
            ['brand' => 'Viewsonic', 'model' => 'PA503S', 'resolution' => '1024x768', 'lumens' => 3600, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Eiki', 'model' => 'EK-301W', 'resolution' => '1024x768', 'lumens' => 3300, 'vga' => true, 'hdmi' => false],
            ['brand' => 'Planar', 'model' => 'PXL2230MW', 'resolution' => '1920x1200', 'lumens' => 5500, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Vivitek', 'model' => 'H1180HD', 'resolution' => '1920x1080', 'lumens' => 3000, 'vga' => true, 'hdmi' => true],
            ['brand' => 'Canon', 'model' => 'LV-7490', 'resolution' => '1024x768', 'lumens' => 3000, 'vga' => true, 'hdmi' => false],
            ['brand' => 'Hitachi', 'model' => 'CP-X8160', 'resolution' => '1024x768', 'lumens' => 5000, 'vga' => true, 'hdmi' => false],
            ['brand' => 'Toshiba', 'model' => 'TLP-WM15', 'resolution' => '1024x768', 'lumens' => 2000, 'vga' => true, 'hdmi' => false],
        ];

        foreach ($projectorModels as $modelData) {
            ProjectorModel::firstOrCreate($modelData);
        }

        $this->command->info('✅ Modelos de Proyectores creados correctamente (25 modelos).');
    }
}
