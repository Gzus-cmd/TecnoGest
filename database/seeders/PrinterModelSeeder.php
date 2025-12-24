<?php

namespace Database\Seeders;

use App\Models\PrinterModel;
use Illuminate\Database\Seeder;

class PrinterModelSeeder extends Seeder
{
    public function run(): void
    {
        $printerModels = [
            ['brand' => 'HP', 'model' => 'LaserJet Enterprise M454', 'type' => 'Láser Color', 'color' => true, 'scanner' => false, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Canon', 'model' => 'imagePRESS C256', 'type' => 'Inyección Color', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Brother', 'model' => 'HL-L8360CDW', 'type' => 'Láser Color', 'color' => true, 'scanner' => false, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Xerox', 'model' => 'Versant 180', 'type' => 'Digitales', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Ricoh', 'model' => 'MP C3004', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Lexmark', 'model' => 'MS826', 'type' => 'Láser B/N', 'color' => false, 'scanner' => false, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Konica Minolta', 'model' => 'bizhub 3110', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Toshiba', 'model' => 'e-STUDIO 5516AC', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Epson', 'model' => 'WorkForce Enterprise', 'type' => 'Inyección', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Kyocera', 'model' => 'ECOSYS M6635cidn', 'type' => 'Láser Color', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Sharp', 'model' => 'MX-C406', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Develop', 'model' => 'ineo+ 3110', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'HP', 'model' => 'OfficeJet Pro 9015', 'type' => 'Inyección', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Canon', 'model' => 'MF445DW', 'type' => 'Láser B/N', 'color' => false, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Brother', 'model' => 'MFC-L9550CDWT', 'type' => 'Láser Color', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Xerox', 'model' => 'AltaLink C8145', 'type' => 'Digitales', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'OKI', 'model' => 'MC873dn', 'type' => 'Láser Color', 'color' => true, 'scanner' => false, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Ricoh', 'model' => 'MP C5503SP', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Panasonic', 'model' => 'DP-C306', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Lanier', 'model' => 'MP C3504', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Triumph', 'model' => 'DFC-525', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Gestetner', 'model' => 'MP C3003', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Savin', 'model' => 'MP C3504', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Muratec', 'model' => 'MFX-C2265', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
            ['brand' => 'Nashuatec', 'model' => 'DSm 725e', 'type' => 'Multifuncional', 'color' => true, 'scanner' => true, 'wifi' => true, 'ethernet' => true],
        ];

        foreach ($printerModels as $modelData) {
            PrinterModel::firstOrCreate($modelData);
        }

        $this->command->info('✅ Modelos de Impresoras creados correctamente (25 modelos).');
    }
}
