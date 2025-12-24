<?php

namespace Database\Seeders;

use App\Models\ProjectorModel;
use Illuminate\Database\Seeder;

class ProjectorModelSeeder extends Seeder
{
    public function run(): void
    {
        $projectorModels = [
            ['model' => 'Epson EB-2250U', 'resolution' => '1920x1200', 'lumens' => 5000, 'vga' => true, 'hdmi' => true],
            ['model' => 'Sony VPL-FHZ75', 'resolution' => '1920x1200', 'lumens' => 6000, 'vga' => true, 'hdmi' => true],
            ['model' => 'Panasonic PT-RZ870', 'resolution' => '1920x1200', 'lumens' => 7000, 'vga' => true, 'hdmi' => true],
            ['model' => 'Christie DHD850', 'resolution' => '2560x1440', 'lumens' => 8500, 'vga' => false, 'hdmi' => true],
            ['model' => 'Barco F50', 'resolution' => '1920x1080', 'lumens' => 5000, 'vga' => true, 'hdmi' => true],
            ['model' => 'Optoma EH412', 'resolution' => '1920x1080', 'lumens' => 4500, 'vga' => true, 'hdmi' => true],
            ['model' => 'Benq MH534A', 'resolution' => '1920x1080', 'lumens' => 3600, 'vga' => true, 'hdmi' => true],
            ['model' => 'Casio XJ-F10X', 'resolution' => '1024x768', 'lumens' => 3000, 'vga' => true, 'hdmi' => false],
            ['model' => 'Acer P5630', 'resolution' => '1920x1080', 'lumens' => 4500, 'vga' => true, 'hdmi' => true],
            ['model' => 'ASUS PA148CTC', 'resolution' => '1920x1080', 'lumens' => 250, 'vga' => false, 'hdmi' => true],
            ['model' => 'InFocus IN119HDx', 'resolution' => '1920x1080', 'lumens' => 3800, 'vga' => true, 'hdmi' => true],
            ['model' => 'NEC M403H', 'resolution' => '1920x1200', 'lumens' => 4200, 'vga' => true, 'hdmi' => true],
            ['model' => 'Mitsubishi XD8100', 'resolution' => '1920x1440', 'lumens' => 5500, 'vga' => true, 'hdmi' => true],
            ['model' => 'Sanyo PLV-Z2000', 'resolution' => '1920x1080', 'lumens' => 2000, 'vga' => true, 'hdmi' => false],
            ['model' => 'Yaber Y60', 'resolution' => '1280x720', 'lumens' => 6000, 'vga' => false, 'hdmi' => true],
            ['model' => 'VANKYO Leisure 3W', 'resolution' => '800x600', 'lumens' => 3600, 'vga' => false, 'hdmi' => true],
            ['model' => 'Anker Nebula Cosmos', 'resolution' => '1920x1080', 'lumens' => 1200, 'vga' => false, 'hdmi' => false],
            ['model' => 'Philips NeoPix Ultra 2', 'resolution' => '1920x1080', 'lumens' => 1000, 'vga' => false, 'hdmi' => true],
            ['model' => 'Viewsonic PA503S', 'resolution' => '1024x768', 'lumens' => 3600, 'vga' => true, 'hdmi' => true],
            ['model' => 'Eiki EK-301W', 'resolution' => '1024x768', 'lumens' => 3300, 'vga' => true, 'hdmi' => false],
            ['model' => 'Planar PXL2230MW', 'resolution' => '1920x1200', 'lumens' => 5500, 'vga' => true, 'hdmi' => true],
            ['model' => 'Vivitek H1180HD', 'resolution' => '1920x1080', 'lumens' => 3000, 'vga' => true, 'hdmi' => true],
            ['model' => 'Canon LV-7490', 'resolution' => '1024x768', 'lumens' => 3000, 'vga' => true, 'hdmi' => false],
            ['model' => 'Hitachi CP-X8160', 'resolution' => '1024x768', 'lumens' => 5000, 'vga' => true, 'hdmi' => false],
            ['model' => 'Toshiba TLP-WM15', 'resolution' => '1024x768', 'lumens' => 2000, 'vga' => true, 'hdmi' => false],
        ];

        foreach ($projectorModels as $modelData) {
            ProjectorModel::firstOrCreate($modelData);
        }

        $this->command->info('✅ Modelos de Proyectores creados correctamente (25 modelos).');
    }
}
