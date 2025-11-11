<?php

namespace Database\Seeders;

use App\Models\SparePart;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener un proveedor por defecto
        $provider = Provider::first();
        
        if (!$provider) {
            $this->command->warn('⚠️ No hay proveedores. Ejecuta ProviderSeeder primero.');
            return;
        }

        // Piezas de repuesto para IMPRESORAS
        $printerParts = [
            // Cabezales de Impresión
            ['name' => 'Cabezal HP 953XL Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL', 'description' => 'Cabezal de impresión negro de alto rendimiento', 'cost_price' => 45.00, 'stock' => 15],
            ['name' => 'Cabezal HP 953XL Color', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL', 'description' => 'Cabezal de impresión tricolor', 'cost_price' => 52.00, 'stock' => 12],
            ['name' => 'Cabezal Canon PG-245XL', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PG-245XL', 'description' => 'Cabezal negro de alta capacidad', 'cost_price' => 38.00, 'stock' => 20],
            ['name' => 'Cabezal Canon CL-246XL', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'CL-246XL', 'description' => 'Cabezal tricolor de alta capacidad', 'cost_price' => 42.00, 'stock' => 18],
            ['name' => 'Cabezal Epson T664', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664', 'description' => 'Cabezal original Epson para EcoTank', 'cost_price' => 35.00, 'stock' => 25],
            ['name' => 'Cabezal Brother LC3013', 'type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013', 'description' => 'Cabezal ultra alta capacidad', 'cost_price' => 48.00, 'stock' => 10],
            ['name' => 'Cabezal Xerox 106R03623', 'type' => 'Cabezal de Impresión', 'brand' => 'Xerox', 'model' => '106R03623', 'description' => 'Cabezal negro para Phaser', 'cost_price' => 55.00, 'stock' => 8],
            ['name' => 'Cabezal Ricoh MP C3004', 'type' => 'Cabezal de Impresión', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Cabezal completo CMYK', 'cost_price' => 125.00, 'stock' => 5],

            // Rodillos
            ['name' => 'Rodillo de Transferencia HP M454', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'M454', 'description' => 'Rodillo de transferencia LaserJet', 'cost_price' => 75.00, 'stock' => 10],
            ['name' => 'Rodillo de Fusor Canon IR-ADV', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'IR-ADV', 'description' => 'Rodillo presión fusor', 'cost_price' => 85.00, 'stock' => 8],
            ['name' => 'Rodillo de Alimentación Brother HL-L8360', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'HL-L8360', 'description' => 'Rodillo pick-up roller', 'cost_price' => 25.00, 'stock' => 20],
            ['name' => 'Rodillo de Separación Xerox Versant', 'type' => 'Rodillo', 'brand' => 'Xerox', 'model' => 'Versant 180', 'description' => 'Rodillo separador de papel', 'cost_price' => 45.00, 'stock' => 12],
            ['name' => 'Rodillo de Carga Ricoh MP', 'type' => 'Rodillo', 'brand' => 'Ricoh', 'model' => 'MP Series', 'description' => 'Rodillo de carga primaria', 'cost_price' => 65.00, 'stock' => 10],
            ['name' => 'Rodillo Transfer Konica Minolta', 'type' => 'Rodillo', 'brand' => 'Konica Minolta', 'model' => 'bizhub', 'description' => 'Rodillo de transferencia intermedia', 'cost_price' => 95.00, 'stock' => 6],
            ['name' => 'Rodillo de Presión Epson WorkForce', 'type' => 'Rodillo', 'brand' => 'Epson', 'model' => 'WorkForce', 'description' => 'Rodillo de presión ADF', 'cost_price' => 35.00, 'stock' => 15],
            ['name' => 'Rodillo Pickup Kyocera ECOSYS', 'type' => 'Rodillo', 'brand' => 'Kyocera', 'model' => 'ECOSYS', 'description' => 'Rodillo de alimentación principal', 'cost_price' => 28.00, 'stock' => 18],

            // Fusores
            ['name' => 'Fusor HP LaserJet M454', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'M454', 'description' => 'Unidad fusora completa 110V', 'cost_price' => 180.00, 'stock' => 5],
            ['name' => 'Fusor Canon imagePRESS C256', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'C256', 'description' => 'Fusor assembly completo', 'cost_price' => 350.00, 'stock' => 3],
            ['name' => 'Fusor Brother HL-L8360CDW', 'type' => 'Fusor', 'brand' => 'Brother', 'model' => 'HL-L8360CDW', 'description' => 'Fuser unit 100k páginas', 'cost_price' => 145.00, 'stock' => 7],
            ['name' => 'Fusor Xerox Versant 180', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'Versant 180', 'description' => 'Fusor de alta capacidad', 'cost_price' => 425.00, 'stock' => 2],
            ['name' => 'Fusor Ricoh MP C3004', 'type' => 'Fusor', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Fusor original Ricoh', 'cost_price' => 280.00, 'stock' => 4],
            ['name' => 'Fusor Lexmark MS826', 'type' => 'Fusor', 'brand' => 'Lexmark', 'model' => 'MS826', 'description' => 'Unidad fusora 150k páginas', 'cost_price' => 225.00, 'stock' => 5],
            ['name' => 'Fusor Konica Minolta bizhub', 'type' => 'Fusor', 'brand' => 'Konica Minolta', 'model' => 'bizhub 3110', 'description' => 'Fuser kit completo', 'cost_price' => 315.00, 'stock' => 3],
            ['name' => 'Fusor Toshiba e-STUDIO', 'type' => 'Fusor', 'brand' => 'Toshiba', 'model' => 'e-STUDIO 5516AC', 'description' => 'Fusor para multifuncional', 'cost_price' => 295.00, 'stock' => 4],
        ];

        // Piezas de repuesto para PROYECTORES
        $projectorParts = [
            // Lámparas
            ['name' => 'Lámpara Epson ELPLP96', 'type' => 'Lámpara', 'brand' => 'Epson', 'model' => 'ELPLP96', 'description' => 'Lámpara UHE 380W para EB-2250U', 'cost_price' => 285.00, 'stock' => 8],
            ['name' => 'Lámpara Sony LMP-F331', 'type' => 'Lámpara', 'brand' => 'Sony', 'model' => 'LMP-F331', 'description' => 'Lámpara original VPL-FHZ75', 'cost_price' => 425.00, 'stock' => 5],
            ['name' => 'Lámpara Panasonic ET-LAD510', 'type' => 'Lámpara', 'brand' => 'Panasonic', 'model' => 'ET-LAD510', 'description' => 'Lámpara dual para PT-RZ870', 'cost_price' => 650.00, 'stock' => 3],
            ['name' => 'Lámpara Christie 003-120577-01', 'type' => 'Lámpara', 'brand' => 'Christie', 'model' => '003-120577-01', 'description' => 'Lámpara xenón DHD850', 'cost_price' => 895.00, 'stock' => 2],
            ['name' => 'Lámpara Barco R9832771', 'type' => 'Lámpara', 'brand' => 'Barco', 'model' => 'R9832771', 'description' => 'Lámpara F50 4000h', 'cost_price' => 525.00, 'stock' => 4],
            ['name' => 'Lámpara Optoma BL-FP280I', 'type' => 'Lámpara', 'brand' => 'Optoma', 'model' => 'BL-FP280I', 'description' => 'Lámpara para EH412', 'cost_price' => 195.00, 'stock' => 12],
            ['name' => 'Lámpara BenQ 5J.JGP05.001', 'type' => 'Lámpara', 'brand' => 'BenQ', 'model' => '5J.JGP05.001', 'description' => 'Lámpara MH534A', 'cost_price' => 165.00, 'stock' => 10],
            ['name' => 'Lámpara Casio YL-40', 'type' => 'Lámpara', 'brand' => 'Casio', 'model' => 'YL-40', 'description' => 'Lámpara híbrida laser-LED', 'cost_price' => 385.00, 'stock' => 6],
            ['name' => 'Lámpara Acer MC.JQ511.001', 'type' => 'Lámpara', 'brand' => 'Acer', 'model' => 'MC.JQ511.001', 'description' => 'Lámpara P5630', 'cost_price' => 215.00, 'stock' => 9],
            ['name' => 'Lámpara InFocus SP-LAMP-092', 'type' => 'Lámpara', 'brand' => 'InFocus', 'model' => 'SP-LAMP-092', 'description' => 'Lámpara IN119HDx', 'cost_price' => 145.00, 'stock' => 11],
            ['name' => 'Lámpara NEC NP42LP', 'type' => 'Lámpara', 'brand' => 'NEC', 'model' => 'NP42LP', 'description' => 'Lámpara para M403H', 'cost_price' => 225.00, 'stock' => 7],
            ['name' => 'Lámpara ViewSonic RLC-118', 'type' => 'Lámpara', 'brand' => 'ViewSonic', 'model' => 'RLC-118', 'description' => 'Lámpara PA503S', 'cost_price' => 125.00, 'stock' => 13],

            // Lentes
            ['name' => 'Lente Zoom Epson ELPLM15', 'type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLM15', 'description' => 'Lente medio zoom 2.1-3.7', 'cost_price' => 485.00, 'stock' => 4],
            ['name' => 'Lente Corto Sony VPLL-Z3032', 'type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z3032', 'description' => 'Lente corto alcance', 'cost_price' => 625.00, 'stock' => 3],
            ['name' => 'Lente Largo Panasonic ET-DLE450', 'type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE450', 'description' => 'Lente largo alcance 4.5-7.3', 'cost_price' => 1250.00, 'stock' => 2],
            ['name' => 'Lente Ultra Corto Christie 140-131104-XX', 'type' => 'Lente', 'brand' => 'Christie', 'model' => '140-131104-XX', 'description' => 'Lente ultra short throw', 'cost_price' => 1850.00, 'stock' => 1],
            ['name' => 'Lente Estándar Barco EN12', 'type' => 'Lente', 'brand' => 'Barco', 'model' => 'EN12', 'description' => 'Lente estándar 1.16-1.49', 'cost_price' => 725.00, 'stock' => 3],
            ['name' => 'Lente Optoma BX-DL100', 'type' => 'Lente', 'brand' => 'Optoma', 'model' => 'BX-DL100', 'description' => 'Lente largo alcance', 'cost_price' => 385.00, 'stock' => 5],
            ['name' => 'Lente BenQ LS2ST2', 'type' => 'Lente', 'brand' => 'BenQ', 'model' => 'LS2ST2', 'description' => 'Lente short throw', 'cost_price' => 295.00, 'stock' => 6],
            ['name' => 'Lente NEC NP41ZL', 'type' => 'Lente', 'brand' => 'NEC', 'model' => 'NP41ZL', 'description' => 'Lente zoom estándar', 'cost_price' => 445.00, 'stock' => 4],

            // Filtros de Aire
            ['name' => 'Filtro Aire Epson ELPAF60', 'type' => 'Filtro de Aire', 'brand' => 'Epson', 'model' => 'ELPAF60', 'description' => 'Filtro reemplazo serie EB', 'cost_price' => 12.00, 'stock' => 50],
            ['name' => 'Filtro Aire Sony PSS-AF2', 'type' => 'Filtro de Aire', 'brand' => 'Sony', 'model' => 'PSS-AF2', 'description' => 'Filtro aire VPL series', 'cost_price' => 18.00, 'stock' => 40],
            ['name' => 'Filtro Aire Panasonic ET-RFV410', 'type' => 'Filtro de Aire', 'brand' => 'Panasonic', 'model' => 'ET-RFV410', 'description' => 'Filtro alta eficiencia', 'cost_price' => 25.00, 'stock' => 35],
            ['name' => 'Filtro Aire Christie 003-006641-01', 'type' => 'Filtro de Aire', 'brand' => 'Christie', 'model' => '003-006641-01', 'description' => 'Filtro industrial', 'cost_price' => 32.00, 'stock' => 25],
            ['name' => 'Filtro Aire Barco R9832775', 'type' => 'Filtro de Aire', 'brand' => 'Barco', 'model' => 'R9832775', 'description' => 'Filtro F series', 'cost_price' => 28.00, 'stock' => 30],
            ['name' => 'Filtro Aire Optoma SP.8JQ01GC01', 'type' => 'Filtro de Aire', 'brand' => 'Optoma', 'model' => 'SP.8JQ01GC01', 'description' => 'Filtro estándar', 'cost_price' => 10.00, 'stock' => 60],
            ['name' => 'Filtro Aire BenQ 5J.Y1C05.001', 'type' => 'Filtro de Aire', 'brand' => 'BenQ', 'model' => '5J.Y1C05.001', 'description' => 'Filtro MH series', 'cost_price' => 8.00, 'stock' => 70],
            ['name' => 'Filtro Aire NEC NP02FT', 'type' => 'Filtro de Aire', 'brand' => 'NEC', 'model' => 'NP02FT', 'description' => 'Filtro reemplazo M series', 'cost_price' => 15.00, 'stock' => 45],
            ['name' => 'Filtro Aire ViewSonic VS17423', 'type' => 'Filtro de Aire', 'brand' => 'ViewSonic', 'model' => 'VS17423', 'description' => 'Filtro PA series', 'cost_price' => 9.00, 'stock' => 55],
            ['name' => 'Filtro Aire Acer MC.JLC11.002', 'type' => 'Filtro de Aire', 'brand' => 'Acer', 'model' => 'MC.JLC11.002', 'description' => 'Filtro P series', 'cost_price' => 11.00, 'stock' => 50],
        ];

        // Combinar todas las piezas
        $allParts = array_merge($printerParts, $projectorParts);

        // Crear las piezas
        foreach ($allParts as $part) {
            SparePart::firstOrCreate(
                [
                    'brand' => $part['brand'],
                    'model' => $part['model'],
                    'name' => $part['name']
                ],
                [
                    'type' => $part['type'],
                    'description' => $part['description'],
                    'cost_price' => $part['cost_price'],
                    'stock' => $part['stock'],
                    'provider_id' => $provider->id,
                ]
            );
        }

        $printerCount = count($printerParts);
        $projectorCount = count($projectorParts);
        $total = count($allParts);

        $this->command->info("✅ Piezas de repuesto creadas correctamente:");
        $this->command->info("   📄 Impresoras: {$printerCount} piezas (Cabezales, Rodillos, Fusores)");
        $this->command->info("   📽️  Proyectores: {$projectorCount} piezas (Lámparas, Lentes, Filtros)");
        $this->command->info("   📦 Total: {$total} piezas de repuesto");
    }
}
