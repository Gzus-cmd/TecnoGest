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
        // Obtener proveedores
        $providers = Provider::where('status', true)->get();
        
        if ($providers->isEmpty()) {
            $this->command->warn('⚠️ No hay proveedores activos. Ejecuta ProviderSeeder primero.');
            return;
        }

        // Piezas de repuesto para IMPRESORAS
        $printerParts = [
            // ===== CABEZALES DE IMPRESIÓN (40 items) =====
            // HP
            ['name' => 'Cabezal HP 953XL Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL', 'description' => 'Cabezal de impresión negro de alto rendimiento para OfficeJet Pro', 'cost_price' => 45.00, 'stock' => 15],
            ['name' => 'Cabezal HP 953XL Cian', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL', 'description' => 'Cabezal de impresión cian de alto rendimiento', 'cost_price' => 52.00, 'stock' => 12],
            ['name' => 'Cabezal HP 953XL Magenta', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL', 'description' => 'Cabezal de impresión magenta de alto rendimiento', 'cost_price' => 52.00, 'stock' => 12],
            ['name' => 'Cabezal HP 953XL Amarillo', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL', 'description' => 'Cabezal de impresión amarillo de alto rendimiento', 'cost_price' => 52.00, 'stock' => 12],
            ['name' => 'Cabezal HP 934XL Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '934XL', 'description' => 'Cabezal negro extra capacidad', 'cost_price' => 48.00, 'stock' => 18],
            ['name' => 'Cabezal HP 935XL Tricolor Set', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '935XL', 'description' => 'Set de cabezales tricolor CMY', 'cost_price' => 95.00, 'stock' => 10],
            ['name' => 'Cabezal HP 711 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '711', 'description' => 'Cabezal para DesignJet T120/T520', 'cost_price' => 125.00, 'stock' => 5],
            ['name' => 'Cabezal HP 950XL Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '950XL', 'description' => 'Cabezal negro ultra capacidad', 'cost_price' => 55.00, 'stock' => 14],
            
            // Canon
            ['name' => 'Cabezal Canon PG-245XL Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PG-245XL', 'description' => 'Cabezal negro de alta capacidad para Pixma', 'cost_price' => 38.00, 'stock' => 20],
            ['name' => 'Cabezal Canon CL-246XL Tricolor', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'CL-246XL', 'description' => 'Cabezal tricolor de alta capacidad', 'cost_price' => 42.00, 'stock' => 18],
            ['name' => 'Cabezal Canon PG-243 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PG-243', 'description' => 'Cabezal negro estándar', 'cost_price' => 32.00, 'stock' => 25],
            ['name' => 'Cabezal Canon CL-244 Tricolor', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'CL-244', 'description' => 'Cabezal tricolor estándar', 'cost_price' => 35.00, 'stock' => 22],
            ['name' => 'Cabezal Canon PGI-250XL Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PGI-250XL', 'description' => 'Cabezal pigmento negro XL', 'cost_price' => 45.00, 'stock' => 16],
            ['name' => 'Cabezal Canon CLI-251 CMYK Set', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'CLI-251', 'description' => 'Set completo CMYK para Pixma', 'cost_price' => 85.00, 'stock' => 12],
            ['name' => 'Cabezal Canon PFI-107 Negro Mate', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PFI-107', 'description' => 'Tinta negra mate para ImagePROGRAF', 'cost_price' => 175.00, 'stock' => 6],
            ['name' => 'Cabezal Canon PFI-107 Cian', 'type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PFI-107', 'description' => 'Tinta cian para ImagePROGRAF', 'cost_price' => 175.00, 'stock' => 6],
            
            // Epson
            ['name' => 'Cabezal Epson T664 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664', 'description' => 'Botella de tinta negra para EcoTank L395/L495', 'cost_price' => 35.00, 'stock' => 25],
            ['name' => 'Cabezal Epson T664 Cian', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664', 'description' => 'Botella de tinta cian para EcoTank', 'cost_price' => 35.00, 'stock' => 25],
            ['name' => 'Cabezal Epson T664 Magenta', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664', 'description' => 'Botella de tinta magenta para EcoTank', 'cost_price' => 35.00, 'stock' => 25],
            ['name' => 'Cabezal Epson T664 Amarillo', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664', 'description' => 'Botella de tinta amarilla para EcoTank', 'cost_price' => 35.00, 'stock' => 25],
            ['name' => 'Cabezal Epson T774 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T774', 'description' => 'Tinta negra para M105/M205', 'cost_price' => 28.00, 'stock' => 30],
            ['name' => 'Cabezal Epson 103 CMYK Set', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => '103', 'description' => 'Set completo para L3110/L3150', 'cost_price' => 98.00, 'stock' => 15],
            ['name' => 'Cabezal Epson T544 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T544', 'description' => 'Tinta para L3250/L5290', 'cost_price' => 32.00, 'stock' => 22],
            
            // Brother
            ['name' => 'Cabezal Brother LC3013 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013', 'description' => 'Cabezal ultra alta capacidad negro', 'cost_price' => 48.00, 'stock' => 10],
            ['name' => 'Cabezal Brother LC3013 Cian', 'type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013', 'description' => 'Cabezal ultra alta capacidad cian', 'cost_price' => 48.00, 'stock' => 10],
            ['name' => 'Cabezal Brother LC3013 Magenta', 'type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013', 'description' => 'Cabezal ultra alta capacidad magenta', 'cost_price' => 48.00, 'stock' => 10],
            ['name' => 'Cabezal Brother LC3013 Amarillo', 'type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013', 'description' => 'Cabezal ultra alta capacidad amarillo', 'cost_price' => 48.00, 'stock' => 10],
            ['name' => 'Cabezal Brother LC3019 XXL Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3019', 'description' => 'Cabezal súper alto rendimiento negro', 'cost_price' => 65.00, 'stock' => 8],
            ['name' => 'Cabezal Brother TN760 Tóner', 'type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'TN760', 'description' => 'Tóner negro alto rendimiento', 'cost_price' => 85.00, 'stock' => 12],
            
            // Xerox
            ['name' => 'Cabezal Xerox 106R03623 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Xerox', 'model' => '106R03623', 'description' => 'Tóner negro para Phaser 3330/WorkCentre 3335', 'cost_price' => 55.00, 'stock' => 8],
            ['name' => 'Cabezal Xerox 106R03624 Cian', 'type' => 'Cabezal de Impresión', 'brand' => 'Xerox', 'model' => '106R03624', 'description' => 'Tóner cian alto rendimiento', 'cost_price' => 125.00, 'stock' => 6],
            ['name' => 'Cabezal Xerox 106R02778 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Xerox', 'model' => '106R02778', 'description' => 'Tóner negro extra capacidad', 'cost_price' => 95.00, 'stock' => 7],
            
            // Ricoh
            ['name' => 'Cabezal Ricoh MP C3004 Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Tóner negro para multifuncional', 'cost_price' => 125.00, 'stock' => 5],
            ['name' => 'Cabezal Ricoh MP C3004 Cian', 'type' => 'Cabezal de Impresión', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Tóner cian para multifuncional', 'cost_price' => 125.00, 'stock' => 5],
            ['name' => 'Cabezal Ricoh MP C3004 Magenta', 'type' => 'Cabezal de Impresión', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Tóner magenta para multifuncional', 'cost_price' => 125.00, 'stock' => 5],
            ['name' => 'Cabezal Ricoh MP C3004 Amarillo', 'type' => 'Cabezal de Impresión', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Tóner amarillo para multifuncional', 'cost_price' => 125.00, 'stock' => 5],
            
            // Konica Minolta
            ['name' => 'Cabezal Konica Minolta TN321K Negro', 'type' => 'Cabezal de Impresión', 'brand' => 'Konica Minolta', 'model' => 'TN321K', 'description' => 'Tóner negro para bizhub C224', 'cost_price' => 95.00, 'stock' => 6],
            ['name' => 'Cabezal Konica Minolta TN324C Cian', 'type' => 'Cabezal de Impresión', 'brand' => 'Konica Minolta', 'model' => 'TN324C', 'description' => 'Tóner cian alto rendimiento', 'cost_price' => 115.00, 'stock' => 5],
            ['name' => 'Cabezal Konica Minolta A33K130 Magenta', 'type' => 'Cabezal de Impresión', 'brand' => 'Konica Minolta', 'model' => 'A33K130', 'description' => 'Tóner magenta para bizhub C3110', 'cost_price' => 115.00, 'stock' => 5],

            // ===== RODILLOS (40 items) =====
            // HP Rodillos
            ['name' => 'Rodillo de Transferencia HP M454', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'M454', 'description' => 'Rodillo de transferencia LaserJet Enterprise', 'cost_price' => 75.00, 'stock' => 10],
            ['name' => 'Rodillo de Transferencia HP M479', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'M479', 'description' => 'Rodillo ITB para LaserJet Pro MFP', 'cost_price' => 125.00, 'stock' => 6],
            ['name' => 'Rodillo Pickup HP M402', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'M402', 'description' => 'Rodillo de alimentación pickup roller', 'cost_price' => 35.00, 'stock' => 15],
            ['name' => 'Rodillo Separación HP M402', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'M402', 'description' => 'Rodillo separador de papel', 'cost_price' => 28.00, 'stock' => 18],
            ['name' => 'Rodillo Presión HP M604', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'M604', 'description' => 'Rodillo de presión fusor', 'cost_price' => 95.00, 'stock' => 7],
            ['name' => 'Rodillo Magnético HP M607', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'M607', 'description' => 'Rodillo magnético revelador', 'cost_price' => 65.00, 'stock' => 8],
            ['name' => 'Rodillo Transfer HP CP5525', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'CP5525', 'description' => 'Rodillo transfer belt assembly', 'cost_price' => 185.00, 'stock' => 4],
            ['name' => 'Rodillo Pickup HP P3015', 'type' => 'Rodillo', 'brand' => 'HP', 'model' => 'P3015', 'description' => 'Rodillo alimentación papel bandeja', 'cost_price' => 22.00, 'stock' => 20],
            
            // Canon Rodillos
            ['name' => 'Rodillo de Fusor Canon IR-ADV', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'IR-ADV', 'description' => 'Rodillo presión fusor imageRUNNER', 'cost_price' => 85.00, 'stock' => 8],
            ['name' => 'Rodillo Transferencia Canon IR C3020', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'IR C3020', 'description' => 'Rodillo transferencia primaria', 'cost_price' => 95.00, 'stock' => 6],
            ['name' => 'Rodillo Pickup Canon IR2520', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'IR2520', 'description' => 'Rodillo alimentación cassette', 'cost_price' => 32.00, 'stock' => 15],
            ['name' => 'Rodillo Separación Canon IR2525', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'IR2525', 'description' => 'Rodillo separador multiuso', 'cost_price' => 28.00, 'stock' => 16],
            ['name' => 'Rodillo Registro Canon C256', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'C256', 'description' => 'Rodillo de registro imagePRESS', 'cost_price' => 75.00, 'stock' => 5],
            ['name' => 'Rodillo Carga Canon MF440', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'MF440', 'description' => 'Rodillo carga primaria PCR', 'cost_price' => 45.00, 'stock' => 12],
            ['name' => 'Rodillo Alimentación Canon MF726', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'MF726', 'description' => 'Rodillo pickup DADF', 'cost_price' => 38.00, 'stock' => 10],
            ['name' => 'Rodillo Transfer Canon LBP712', 'type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'LBP712', 'description' => 'Rodillo transferencia secundaria', 'cost_price' => 115.00, 'stock' => 4],
            
            // Brother Rodillos
            ['name' => 'Rodillo de Alimentación Brother HL-L8360', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'HL-L8360', 'description' => 'Rodillo pick-up roller assembly', 'cost_price' => 25.00, 'stock' => 20],
            ['name' => 'Rodillo Separación Brother HL-L8360', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'HL-L8360', 'description' => 'Rodillo separador papel', 'cost_price' => 22.00, 'stock' => 18],
            ['name' => 'Rodillo Tambor Brother DR730', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'DR730', 'description' => 'Unidad de tambor completa', 'cost_price' => 95.00, 'stock' => 8],
            ['name' => 'Rodillo Transfer Brother MFC-L8900', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'MFC-L8900', 'description' => 'Rodillo transferencia belt', 'cost_price' => 75.00, 'stock' => 6],
            ['name' => 'Rodillo Fusor Brother HL-L6200', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'HL-L6200', 'description' => 'Rodillo presión fuser', 'cost_price' => 55.00, 'stock' => 9],
            ['name' => 'Rodillo Pickup Brother MFC-L5700', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'MFC-L5700', 'description' => 'Rodillo alimentación ADF', 'cost_price' => 28.00, 'stock' => 15],
            ['name' => 'Rodillo Registro Brother HL-5450', 'type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'HL-5450', 'description' => 'Rodillo de registro', 'cost_price' => 35.00, 'stock' => 12],
            
            // Xerox Rodillos
            ['name' => 'Rodillo de Separación Xerox Versant', 'type' => 'Rodillo', 'brand' => 'Xerox', 'model' => 'Versant 180', 'description' => 'Rodillo separador de papel alta capacidad', 'cost_price' => 45.00, 'stock' => 12],
            ['name' => 'Rodillo Transfer Xerox WorkCentre 7835', 'type' => 'Rodillo', 'brand' => 'Xerox', 'model' => 'WC 7835', 'description' => 'Rodillo transfer belt assembly', 'cost_price' => 225.00, 'stock' => 3],
            ['name' => 'Rodillo Fusor Xerox Phaser 6510', 'type' => 'Rodillo', 'brand' => 'Xerox', 'model' => 'Phaser 6510', 'description' => 'Rodillo fusor presión', 'cost_price' => 85.00, 'stock' => 6],
            ['name' => 'Rodillo Pickup Xerox 3335', 'type' => 'Rodillo', 'brand' => 'Xerox', 'model' => '3335', 'description' => 'Rodillo alimentación pickup', 'cost_price' => 28.00, 'stock' => 14],
            ['name' => 'Rodillo Separación Xerox 5855', 'type' => 'Rodillo', 'brand' => 'Xerox', 'model' => '5855', 'description' => 'Rodillo retardo separación', 'cost_price' => 38.00, 'stock' => 10],
            ['name' => 'Rodillo Tambor Xerox 101R00555', 'type' => 'Rodillo', 'brand' => 'Xerox', 'model' => '101R00555', 'description' => 'Unidad cilindro fotorreceptor', 'cost_price' => 165.00, 'stock' => 5],
            
            // Ricoh Rodillos
            ['name' => 'Rodillo de Carga Ricoh MP', 'type' => 'Rodillo', 'brand' => 'Ricoh', 'model' => 'MP Series', 'description' => 'Rodillo de carga primaria PCU', 'cost_price' => 65.00, 'stock' => 10],
            ['name' => 'Rodillo Transfer Ricoh MP C3004', 'type' => 'Rodillo', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Rodillo transferencia intermediaria', 'cost_price' => 155.00, 'stock' => 4],
            ['name' => 'Rodillo Pickup Ricoh SP 5210', 'type' => 'Rodillo', 'brand' => 'Ricoh', 'model' => 'SP 5210', 'description' => 'Rodillo alimentación feed', 'cost_price' => 32.00, 'stock' => 12],
            ['name' => 'Rodillo Fusor Ricoh MP 4002', 'type' => 'Rodillo', 'brand' => 'Ricoh', 'model' => 'MP 4002', 'description' => 'Rodillo presión heat roller', 'cost_price' => 125.00, 'stock' => 5],
            ['name' => 'Rodillo Separación Ricoh Aficio 2035', 'type' => 'Rodillo', 'brand' => 'Ricoh', 'model' => 'Aficio 2035', 'description' => 'Rodillo separador multiuso', 'cost_price' => 28.00, 'stock' => 15],
            
            // Otros fabricantes
            ['name' => 'Rodillo Transfer Konica Minolta', 'type' => 'Rodillo', 'brand' => 'Konica Minolta', 'model' => 'bizhub', 'description' => 'Rodillo de transferencia intermedia ITB', 'cost_price' => 95.00, 'stock' => 6],
            ['name' => 'Rodillo de Presión Epson WorkForce', 'type' => 'Rodillo', 'brand' => 'Epson', 'model' => 'WorkForce', 'description' => 'Rodillo de presión ADF scanner', 'cost_price' => 35.00, 'stock' => 15],
            ['name' => 'Rodillo Pickup Kyocera ECOSYS', 'type' => 'Rodillo', 'brand' => 'Kyocera', 'model' => 'ECOSYS', 'description' => 'Rodillo de alimentación principal', 'cost_price' => 28.00, 'stock' => 18],
            ['name' => 'Rodillo Fusor Lexmark MS810', 'type' => 'Rodillo', 'brand' => 'Lexmark', 'model' => 'MS810', 'description' => 'Rodillo presión fuser maintenance kit', 'cost_price' => 75.00, 'stock' => 7],
            ['name' => 'Rodillo Transfer Sharp MX-3070', 'type' => 'Rodillo', 'brand' => 'Sharp', 'model' => 'MX-3070', 'description' => 'Rodillo transferencia belt unit', 'cost_price' => 135.00, 'stock' => 4],

            // ===== FUSORES (40 items) =====
            // HP Fusores
            ['name' => 'Fusor HP LaserJet M454', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'M454', 'description' => 'Unidad fusora completa 110V Enterprise Color', 'cost_price' => 180.00, 'stock' => 5],
            ['name' => 'Fusor HP LaserJet M479', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'M479', 'description' => 'Fuser kit Pro MFP 110V', 'cost_price' => 195.00, 'stock' => 4],
            ['name' => 'Fusor HP LaserJet M607', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'M607', 'description' => 'Maintenance kit fusor 110V', 'cost_price' => 215.00, 'stock' => 6],
            ['name' => 'Fusor HP LaserJet M402', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'M402', 'description' => 'Fuser assembly 110V', 'cost_price' => 165.00, 'stock' => 7],
            ['name' => 'Fusor HP LaserJet P3015', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'P3015', 'description' => 'Kit mantenimiento fusor', 'cost_price' => 145.00, 'stock' => 8],
            ['name' => 'Fusor HP Color LaserJet CP5525', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'CP5525', 'description' => 'Fusor color 110V assembly', 'cost_price' => 285.00, 'stock' => 3],
            ['name' => 'Fusor HP LaserJet M806', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'M806', 'description' => 'Fuser maintenance kit alta capacidad', 'cost_price' => 325.00, 'stock' => 2],
            ['name' => 'Fusor HP LaserJet M527', 'type' => 'Fusor', 'brand' => 'HP', 'model' => 'M527', 'description' => 'Fuser kit Enterprise MFP', 'cost_price' => 245.00, 'stock' => 4],
            
            // Canon Fusores
            ['name' => 'Fusor Canon imagePRESS C256', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'C256', 'description' => 'Fusor assembly completo producción', 'cost_price' => 350.00, 'stock' => 3],
            ['name' => 'Fusor Canon IR-ADV C5540', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'IR-ADV C5540', 'description' => 'Fixing unit imageRUNNER', 'cost_price' => 395.00, 'stock' => 2],
            ['name' => 'Fusor Canon IR2520', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'IR2520', 'description' => 'Unidad fusora imageRUNNER', 'cost_price' => 225.00, 'stock' => 5],
            ['name' => 'Fusor Canon MF440', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'MF440', 'description' => 'Fuser unit imageCLASS MF', 'cost_price' => 185.00, 'stock' => 6],
            ['name' => 'Fusor Canon LBP712', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'LBP712', 'description' => 'Fixing assembly i-SENSYS', 'cost_price' => 265.00, 'stock' => 4],
            ['name' => 'Fusor Canon IR-ADV 4545', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'IR-ADV 4545', 'description' => 'Fusor multifuncional blanco y negro', 'cost_price' => 295.00, 'stock' => 3],
            ['name' => 'Fusor Canon MF729', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'MF729', 'description' => 'Fuser imageCLASS color', 'cost_price' => 215.00, 'stock' => 5],
            ['name' => 'Fusor Canon IR C3020', 'type' => 'Fusor', 'brand' => 'Canon', 'model' => 'IR C3020', 'description' => 'Fixing unit color imageRUNNER', 'cost_price' => 325.00, 'stock' => 3],
            
            // Brother Fusores
            ['name' => 'Fusor Brother HL-L8360CDW', 'type' => 'Fusor', 'brand' => 'Brother', 'model' => 'HL-L8360CDW', 'description' => 'Fuser unit 100k páginas color', 'cost_price' => 145.00, 'stock' => 7],
            ['name' => 'Fusor Brother MFC-L8900CDW', 'type' => 'Fusor', 'brand' => 'Brother', 'model' => 'MFC-L8900CDW', 'description' => 'Fuser assembly MFP color', 'cost_price' => 165.00, 'stock' => 6],
            ['name' => 'Fusor Brother HL-L6200DW', 'type' => 'Fusor', 'brand' => 'Brother', 'model' => 'HL-L6200DW', 'description' => 'Fuser kit monocromático', 'cost_price' => 125.00, 'stock' => 8],
            ['name' => 'Fusor Brother MFC-L5700DW', 'type' => 'Fusor', 'brand' => 'Brother', 'model' => 'MFC-L5700DW', 'description' => 'Fuser unit multifunción', 'cost_price' => 135.00, 'stock' => 7],
            ['name' => 'Fusor Brother HL-5450DN', 'type' => 'Fusor', 'brand' => 'Brother', 'model' => 'HL-5450DN', 'description' => 'Fuser assembly workgroup', 'cost_price' => 115.00, 'stock' => 9],
            ['name' => 'Fusor Brother MFC-L9570CDW', 'type' => 'Fusor', 'brand' => 'Brother', 'model' => 'MFC-L9570CDW', 'description' => 'Fuser alto rendimiento color', 'cost_price' => 185.00, 'stock' => 5],
            
            // Xerox Fusores
            ['name' => 'Fusor Xerox Versant 180', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'Versant 180', 'description' => 'Fusor de alta capacidad producción', 'cost_price' => 425.00, 'stock' => 2],
            ['name' => 'Fusor Xerox WorkCentre 7835', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'WC 7835', 'description' => 'Fuser module assembly', 'cost_price' => 365.00, 'stock' => 3],
            ['name' => 'Fusor Xerox Phaser 6510', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'Phaser 6510', 'description' => 'Fuser cartridge color', 'cost_price' => 195.00, 'stock' => 5],
            ['name' => 'Fusor Xerox WorkCentre 3335', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => '3335', 'description' => 'Fuser kit maintenance', 'cost_price' => 165.00, 'stock' => 6],
            ['name' => 'Fusor Xerox VersaLink C405', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'C405', 'description' => 'Fuser assembly color MFP', 'cost_price' => 225.00, 'stock' => 4],
            ['name' => 'Fusor Xerox Phaser 5550', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => '5550', 'description' => 'Maintenance kit fusor', 'cost_price' => 185.00, 'stock' => 5],
            ['name' => 'Fusor Xerox AltaLink C8155', 'type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'C8155', 'description' => 'Fuser web assembly', 'cost_price' => 445.00, 'stock' => 2],
            
            // Ricoh Fusores
            ['name' => 'Fusor Ricoh MP C3004', 'type' => 'Fusor', 'brand' => 'Ricoh', 'model' => 'MP C3004', 'description' => 'Fusor original Ricoh color', 'cost_price' => 280.00, 'stock' => 4],
            ['name' => 'Fusor Ricoh MP 4002', 'type' => 'Fusor', 'brand' => 'Ricoh', 'model' => 'MP 4002', 'description' => 'Fuser unit monocromático', 'cost_price' => 245.00, 'stock' => 5],
            ['name' => 'Fusor Ricoh SP 5210', 'type' => 'Fusor', 'brand' => 'Ricoh', 'model' => 'SP 5210', 'description' => 'Fuser kit maintenance', 'cost_price' => 185.00, 'stock' => 6],
            ['name' => 'Fusor Ricoh Aficio MP C2551', 'type' => 'Fusor', 'brand' => 'Ricoh', 'model' => 'MP C2551', 'description' => 'Fuser oil unit', 'cost_price' => 265.00, 'stock' => 4],
            ['name' => 'Fusor Ricoh MP 6002', 'type' => 'Fusor', 'brand' => 'Ricoh', 'model' => 'MP 6002', 'description' => 'Fuser assembly alta velocidad', 'cost_price' => 325.00, 'stock' => 3],
            
            // Otros fabricantes
            ['name' => 'Fusor Lexmark MS826', 'type' => 'Fusor', 'brand' => 'Lexmark', 'model' => 'MS826', 'description' => 'Unidad fusora 150k páginas', 'cost_price' => 225.00, 'stock' => 5],
            ['name' => 'Fusor Konica Minolta bizhub', 'type' => 'Fusor', 'brand' => 'Konica Minolta', 'model' => 'bizhub 3110', 'description' => 'Fuser kit completo color', 'cost_price' => 315.00, 'stock' => 3],
            ['name' => 'Fusor Toshiba e-STUDIO', 'type' => 'Fusor', 'brand' => 'Toshiba', 'model' => 'e-STUDIO 5516AC', 'description' => 'Fusor para multifuncional color', 'cost_price' => 295.00, 'stock' => 4],
            ['name' => 'Fusor Kyocera ECOSYS M6635', 'type' => 'Fusor', 'brand' => 'Kyocera', 'model' => 'M6635', 'description' => 'Fuser unit MK-5205', 'cost_price' => 205.00, 'stock' => 5],
            ['name' => 'Fusor Sharp MX-3070', 'type' => 'Fusor', 'brand' => 'Sharp', 'model' => 'MX-3070', 'description' => 'Fuser kit multifunción', 'cost_price' => 285.00, 'stock' => 4],
        ];

        // Piezas de repuesto para PROYECTORES
        $projectorParts = [
            // ===== LÁMPARAS DE PROYECTOR (50 items) =====
            // Epson Lámparas
            ['name' => 'Lámpara Epson ELPLP96', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP96', 'description' => 'Lámpara UHE 380W para EB-2250U, vida útil 4000h', 'cost_price' => 285.00, 'stock' => 8],
            ['name' => 'Lámpara Epson ELPLP88', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP88', 'description' => 'Lámpara para PowerLite 955WH, 97/98/99W', 'cost_price' => 195.00, 'stock' => 12],
            ['name' => 'Lámpara Epson ELPLP89', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP89', 'description' => 'Lámpara PowerLite 5350/5300 series', 'cost_price' => 245.00, 'stock' => 10],
            ['name' => 'Lámpara Epson ELPLP95', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP95', 'description' => 'Lámpara para EB-2155W/2165W/2245U', 'cost_price' => 275.00, 'stock' => 9],
            ['name' => 'Lámpara Epson ELPLP87', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP87', 'description' => 'Lámpara PowerLite 520/525W/530/535W', 'cost_price' => 165.00, 'stock' => 15],
            ['name' => 'Lámpara Epson ELPLP97', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP97', 'description' => 'Lámpara para EB-X49/W49/U42', 'cost_price' => 185.00, 'stock' => 13],
            ['name' => 'Lámpara Epson ELPLP78', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP78', 'description' => 'Lámpara EX/PowerLite 955W/965/97/98/99W', 'cost_price' => 175.00, 'stock' => 14],
            ['name' => 'Lámpara Epson ELPLP75', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP75', 'description' => 'Lámpara PowerLite 1940W/1945W/1950/1955', 'cost_price' => 225.00, 'stock' => 8],
            ['name' => 'Lámpara Epson ELPLP93', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP93', 'description' => 'Lámpara para EB-530/535W/536Wi', 'cost_price' => 155.00, 'stock' => 16],
            ['name' => 'Lámpara Epson ELPLP94', 'type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP94', 'description' => 'Lámpara EB-1780W/1781W/1785W', 'cost_price' => 265.00, 'stock' => 7],
            
            // Sony Lámparas
            ['name' => 'Lámpara Sony LMP-F331', 'type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-F331', 'description' => 'Lámpara original VPL-FHZ75, 2000h nominal', 'cost_price' => 425.00, 'stock' => 5],
            ['name' => 'Lámpara Sony LMP-E212', 'type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-E212', 'description' => 'Lámpara VPL-EX255/EX275/SW536/SW631', 'cost_price' => 285.00, 'stock' => 8],
            ['name' => 'Lámpara Sony LMP-D214', 'type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-D214', 'description' => 'Lámpara VPL-DX145/DX147', 'cost_price' => 195.00, 'stock' => 11],
            ['name' => 'Lámpara Sony LMP-E211', 'type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-E211', 'description' => 'Lámpara VPL-EX100/EX120/EX145/EX175', 'cost_price' => 225.00, 'stock' => 9],
            ['name' => 'Lámpara Sony LMP-D213', 'type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-D213', 'description' => 'Lámpara VPL-DW120/DW125/DX120/DX140', 'cost_price' => 215.00, 'stock' => 10],
            ['name' => 'Lámpara Sony LMP-C281', 'type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-C281', 'description' => 'Lámpara VPL-CH375/CH370', 'cost_price' => 385.00, 'stock' => 6],
            ['name' => 'Lámpara Sony LMP-F272', 'type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-F272', 'description' => 'Lámpara VPL-FX37/FH36/FH31', 'cost_price' => 345.00, 'stock' => 5],
            
            // Panasonic Lámparas
            ['name' => 'Lámpara Panasonic ET-LAD510', 'type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAD510', 'description' => 'Lámpara dual para PT-RZ870/RZ970, 20000h', 'cost_price' => 650.00, 'stock' => 3],
            ['name' => 'Lámpara Panasonic ET-LAL500', 'type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAL500', 'description' => 'Lámpara PT-LB360/LB382/LW312', 'cost_price' => 225.00, 'stock' => 9],
            ['name' => 'Lámpara Panasonic ET-LAV400', 'type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAV400', 'description' => 'Lámpara PT-VW530/VW535N/VX605N', 'cost_price' => 265.00, 'stock' => 7],
            ['name' => 'Lámpara Panasonic ET-LAE300', 'type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAE300', 'description' => 'Lámpara PT-EW730/EX800', 'cost_price' => 295.00, 'stock' => 6],
            ['name' => 'Lámpara Panasonic ET-LAD70', 'type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAD70', 'description' => 'Lámpara PT-DZ780/DW740/DX810', 'cost_price' => 485.00, 'stock' => 4],
            ['name' => 'Lámpara Panasonic ET-LAV200', 'type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAV200', 'description' => 'Lámpara PT-VW435N/VW430/VX505N', 'cost_price' => 245.00, 'stock' => 8],
            ['name' => 'Lámpara Panasonic ET-LAA410', 'type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAA410', 'description' => 'Lámpara PT-AE8000/AT6000E', 'cost_price' => 335.00, 'stock' => 5],
            
            // Christie Lámparas
            ['name' => 'Lámpara Christie 003-120577-01', 'type' => 'Lámpara de Proyector', 'brand' => 'Christie', 'model' => '003-120577-01', 'description' => 'Lámpara xenón DHD850, uso cinematográfico', 'cost_price' => 895.00, 'stock' => 2],
            ['name' => 'Lámpara Christie 003-120507-01', 'type' => 'Lámpara de Proyector', 'brand' => 'Christie', 'model' => '003-120507-01', 'description' => 'Lámpara para LWU701i/LW751i/LX801i', 'cost_price' => 425.00, 'stock' => 4],
            ['name' => 'Lámpara Christie 003-120531-01', 'type' => 'Lámpara de Proyector', 'brand' => 'Christie', 'model' => '003-120531-01', 'description' => 'Lámpara D12WU-H/D12HD-H', 'cost_price' => 525.00, 'stock' => 3],
            ['name' => 'Lámpara Christie 003-005237-01', 'type' => 'Lámpara de Proyector', 'brand' => 'Christie', 'model' => '003-005237-01', 'description' => 'Lámpara LWU420/LW401/LX501', 'cost_price' => 365.00, 'stock' => 5],
            
            // Barco Lámparas
            ['name' => 'Lámpara Barco R9832771', 'type' => 'Lámpara de Proyector', 'brand' => 'Barco', 'model' => 'R9832771', 'description' => 'Lámpara F50 4000h, alta luminosidad', 'cost_price' => 525.00, 'stock' => 4],
            ['name' => 'Lámpara Barco R9832752', 'type' => 'Lámpara de Proyector', 'brand' => 'Barco', 'model' => 'R9832752', 'description' => 'Lámpara F32/F35 series', 'cost_price' => 445.00, 'stock' => 5],
            ['name' => 'Lámpara Barco R9832773', 'type' => 'Lámpara de Proyector', 'brand' => 'Barco', 'model' => 'R9832773', 'description' => 'Lámpara G60-W10/W12', 'cost_price' => 685.00, 'stock' => 3],
            
            // Optoma Lámparas
            ['name' => 'Lámpara Optoma BL-FP280I', 'type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'BL-FP280I', 'description' => 'Lámpara para EH412/W412/X412 3500h', 'cost_price' => 195.00, 'stock' => 12],
            ['name' => 'Lámpara Optoma BL-FP190E', 'type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'BL-FP190E', 'description' => 'Lámpara HD141X/HD26/GT1080', 'cost_price' => 145.00, 'stock' => 15],
            ['name' => 'Lámpara Optoma SP.71P01GC01', 'type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'SP.71P01GC01', 'description' => 'Lámpara HD20/HD200X/HD20-LV', 'cost_price' => 165.00, 'stock' => 13],
            ['name' => 'Lámpara Optoma BL-FU310B', 'type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'BL-FU310B', 'description' => 'Lámpara EH500/DH1017/X600', 'cost_price' => 285.00, 'stock' => 8],
            ['name' => 'Lámpara Optoma BL-FP240E', 'type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'BL-FP240E', 'description' => 'Lámpara UHD65/UHD60', 'cost_price' => 325.00, 'stock' => 6],
            
            // BenQ Lámparas
            ['name' => 'Lámpara BenQ 5J.JGP05.001', 'type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.JGP05.001', 'description' => 'Lámpara MH534A/MS535A/MW535A', 'cost_price' => 165.00, 'stock' => 10],
            ['name' => 'Lámpara BenQ 5J.J9H05.001', 'type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.J9H05.001', 'description' => 'Lámpara W1070+/W1080ST+', 'cost_price' => 185.00, 'stock' => 11],
            ['name' => 'Lámpara BenQ 5J.JEE05.001', 'type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.JEE05.001', 'description' => 'Lámpara HT2150ST/W1110', 'cost_price' => 195.00, 'stock' => 9],
            ['name' => 'Lámpara BenQ 5J.J7L05.001', 'type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.J7L05.001', 'description' => 'Lámpara W1070/W1080ST', 'cost_price' => 175.00, 'stock' => 12],
            ['name' => 'Lámpara BenQ 5J.JAH05.001', 'type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.JAH05.001', 'description' => 'Lámpara MH680/TH680/TH681', 'cost_price' => 205.00, 'stock' => 8],
            
            // Casio Lámparas (Híbridas Laser-LED)
            ['name' => 'Lámpara Casio YL-40', 'type' => 'Lámpara de Proyector', 'brand' => 'Casio', 'model' => 'YL-40', 'description' => 'Lámpara híbrida laser-LED XJ-F10X 20000h', 'cost_price' => 385.00, 'stock' => 6],
            ['name' => 'Lámpara Casio YL-41', 'type' => 'Lámpara de Proyector', 'brand' => 'Casio', 'model' => 'YL-41', 'description' => 'Módulo láser XJ-A256/A257', 'cost_price' => 425.00, 'stock' => 5],
            
            // Acer Lámparas
            ['name' => 'Lámpara Acer MC.JQ511.001', 'type' => 'Lámpara de Proyector', 'brand' => 'Acer', 'model' => 'MC.JQ511.001', 'description' => 'Lámpara P5630/P5530/P5530i', 'cost_price' => 215.00, 'stock' => 9],
            ['name' => 'Lámpara Acer MC.JKL11.001', 'type' => 'Lámpara de Proyector', 'brand' => 'Acer', 'model' => 'MC.JKL11.001', 'description' => 'Lámpara S1283e/S1283Hne', 'cost_price' => 155.00, 'stock' => 13],
            ['name' => 'Lámpara Acer MC.JH511.004', 'type' => 'Lámpara de Proyector', 'brand' => 'Acer', 'model' => 'MC.JH511.004', 'description' => 'Lámpara P1173/X1173/X1173A', 'cost_price' => 145.00, 'stock' => 14],
            
            // InFocus Lámparas
            ['name' => 'Lámpara InFocus SP-LAMP-092', 'type' => 'Lámpara de Proyector', 'brand' => 'InFocus', 'model' => 'SP-LAMP-092', 'description' => 'Lámpara IN119HDx/IN119HDxST 4000h', 'cost_price' => 145.00, 'stock' => 11],
            ['name' => 'Lámpara InFocus SP-LAMP-093', 'type' => 'Lámpara de Proyector', 'brand' => 'InFocus', 'model' => 'SP-LAMP-093', 'description' => 'Lámpara IN112x/IN114x/IN116x', 'cost_price' => 135.00, 'stock' => 12],
            ['name' => 'Lámpara InFocus SP-LAMP-087', 'type' => 'Lámpara de Proyector', 'brand' => 'InFocus', 'model' => 'SP-LAMP-087', 'description' => 'Lámpara IN124a/IN124STa/IN126a', 'cost_price' => 155.00, 'stock' => 10],
            
            // NEC Lámparas
            ['name' => 'Lámpara NEC NP42LP', 'type' => 'Lámpara de Proyector', 'brand' => 'NEC', 'model' => 'NP42LP', 'description' => 'Lámpara para M403H/M363X/M323H 3000h', 'cost_price' => 225.00, 'stock' => 7],
            ['name' => 'Lámpara NEC NP26LP', 'type' => 'Lámpara de Proyector', 'brand' => 'NEC', 'model' => 'NP26LP', 'description' => 'Lámpara PA622U/PA672W/PA722X', 'cost_price' => 275.00, 'stock' => 6],
            ['name' => 'Lámpara NEC NP35LP', 'type' => 'Lámpara de Proyector', 'brand' => 'NEC', 'model' => 'NP35LP', 'description' => 'Lámpara V332W/V332X/V302H', 'cost_price' => 185.00, 'stock' => 9],
            
            // ViewSonic Lámparas
            ['name' => 'Lámpara ViewSonic RLC-118', 'type' => 'Lámpara de Proyector', 'brand' => 'ViewSonic', 'model' => 'RLC-118', 'description' => 'Lámpara PA503S/PG603X 3500h', 'cost_price' => 125.00, 'stock' => 13],
            ['name' => 'Lámpara ViewSonic RLC-117', 'type' => 'Lámpara de Proyector', 'brand' => 'ViewSonic', 'model' => 'RLC-117', 'description' => 'Lámpara PA503W/PG703W', 'cost_price' => 135.00, 'stock' => 12],
            
            // ===== LENTES DE PROYECTOR (30 items) =====
            // Epson Lentes
            ['name' => 'Lente Zoom Epson ELPLM15', 'type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLM15', 'description' => 'Lente medio zoom 2.1-3.7:1 EB-G series', 'cost_price' => 485.00, 'stock' => 4],
            ['name' => 'Lente Corto Epson ELPLU03', 'type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLU03', 'description' => 'Lente ultra corto alcance 0.35:1', 'cost_price' => 1285.00, 'stock' => 2],
            ['name' => 'Lente Largo Epson ELPLM11', 'type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLM11', 'description' => 'Lente largo alcance 6.0-9.0:1', 'cost_price' => 865.00, 'stock' => 3],
            ['name' => 'Lente Estándar Epson ELPLU02', 'type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLU02', 'description' => 'Lente corto fijo 0.79:1', 'cost_price' => 625.00, 'stock' => 4],
            ['name' => 'Lente Zoom Epson ELPLM10', 'type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLM10', 'description' => 'Lente medio zoom 4.4-6.9:1', 'cost_price' => 745.00, 'stock' => 3],
            ['name' => 'Lente Wide Epson ELPLW08', 'type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLW08', 'description' => 'Lente gran angular 1.3-1.7:1', 'cost_price' => 585.00, 'stock' => 4],
            
            // Sony Lentes
            ['name' => 'Lente Corto Sony VPLL-Z3032', 'type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z3032', 'description' => 'Lente corto alcance 0.85-1.02:1 VPL-F series', 'cost_price' => 625.00, 'stock' => 3],
            ['name' => 'Lente Medio Sony VPLL-Z4045', 'type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z4045', 'description' => 'Lente medio zoom 1.85-2.73:1', 'cost_price' => 765.00, 'stock' => 3],
            ['name' => 'Lente Largo Sony VPLL-Z4111', 'type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z4111', 'description' => 'Lente largo zoom 5.0-8.0:1', 'cost_price' => 1125.00, 'stock' => 2],
            ['name' => 'Lente Ultra Corto Sony VPLL-Z2024', 'type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z2024', 'description' => 'Lente ultra corto 0.38:1 fijo', 'cost_price' => 1485.00, 'stock' => 1],
            
            // Panasonic Lentes
            ['name' => 'Lente Largo Panasonic ET-DLE450', 'type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE450', 'description' => 'Lente largo alcance 4.5-7.3:1 PT-D/DZ series', 'cost_price' => 1250.00, 'stock' => 2],
            ['name' => 'Lente Estándar Panasonic ET-DLE250', 'type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE250', 'description' => 'Lente zoom 2.4-3.7:1', 'cost_price' => 685.00, 'stock' => 3],
            ['name' => 'Lente Corto Panasonic ET-DLE150', 'type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE150', 'description' => 'Lente zoom 1.4-2.2:1', 'cost_price' => 795.00, 'stock' => 3],
            ['name' => 'Lente Ultra Largo Panasonic ET-DLE055', 'type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE055', 'description' => 'Lente ultra largo alcance 0.8:1 fijo', 'cost_price' => 1650.00, 'stock' => 1],
            ['name' => 'Lente Wide Panasonic ET-DLE085', 'type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE085', 'description' => 'Lente gran angular 0.8-1.0:1', 'cost_price' => 925.00, 'stock' => 2],
            
            // Christie Lentes
            ['name' => 'Lente Ultra Corto Christie 140-131104-XX', 'type' => 'Lente', 'brand' => 'Christie', 'model' => '140-131104-XX', 'description' => 'Lente ultra short throw 0.37:1 fijo', 'cost_price' => 1850.00, 'stock' => 1],
            ['name' => 'Lente Zoom Christie 140-102105-XX', 'type' => 'Lente', 'brand' => 'Christie', 'model' => '140-102105-XX', 'description' => 'Lente zoom 1.8-2.4:1 LWU/LX series', 'cost_price' => 985.00, 'stock' => 2],
            ['name' => 'Lente Largo Christie 140-104106-XX', 'type' => 'Lente', 'brand' => 'Christie', 'model' => '140-104106-XX', 'description' => 'Lente largo alcance 4.5-7.5:1', 'cost_price' => 1425.00, 'stock' => 2],
            
            // Barco Lentes
            ['name' => 'Lente Estándar Barco EN12', 'type' => 'Lente', 'brand' => 'Barco', 'model' => 'EN12', 'description' => 'Lente estándar 1.16-1.49:1 F series', 'cost_price' => 725.00, 'stock' => 3],
            ['name' => 'Lente Medio Barco EN13', 'type' => 'Lente', 'brand' => 'Barco', 'model' => 'EN13', 'description' => 'Lente medio zoom 1.49-2.24:1', 'cost_price' => 845.00, 'stock' => 2],
            ['name' => 'Lente Largo Barco EN17', 'type' => 'Lente', 'brand' => 'Barco', 'model' => 'EN17', 'description' => 'Lente largo alcance 5.5-8.5:1', 'cost_price' => 1385.00, 'stock' => 2],
            
            // Optoma Lentes
            ['name' => 'Lente Optoma BX-DL100', 'type' => 'Lente', 'brand' => 'Optoma', 'model' => 'BX-DL100', 'description' => 'Lente largo alcance 3.16-5.05:1', 'cost_price' => 385.00, 'stock' => 5],
            ['name' => 'Lente Corto Optoma BX-CTA01', 'type' => 'Lente', 'brand' => 'Optoma', 'model' => 'BX-CTA01', 'description' => 'Lente short throw 0.65-0.76:1', 'cost_price' => 565.00, 'stock' => 4],
            
            // BenQ Lentes
            ['name' => 'Lente BenQ LS2ST2', 'type' => 'Lente', 'brand' => 'BenQ', 'model' => 'LS2ST2', 'description' => 'Lente short throw 0.78:1 fijo', 'cost_price' => 295.00, 'stock' => 6],
            ['name' => 'Lente BenQ LS2LT2', 'type' => 'Lente', 'brand' => 'BenQ', 'model' => 'LS2LT2', 'description' => 'Lente largo throw 2.0-3.0:1', 'cost_price' => 425.00, 'stock' => 4],
            
            // NEC Lentes
            ['name' => 'Lente NEC NP41ZL', 'type' => 'Lente', 'brand' => 'NEC', 'model' => 'NP41ZL', 'description' => 'Lente zoom estándar 1.6-3.1:1 PA series', 'cost_price' => 445.00, 'stock' => 4],
            ['name' => 'Lente NEC NP42ZL', 'type' => 'Lente', 'brand' => 'NEC', 'model' => 'NP42ZL', 'description' => 'Lente largo zoom 3.0-6.2:1', 'cost_price' => 625.00, 'stock' => 3],
            ['name' => 'Lente NEC NP43ZL', 'type' => 'Lente', 'brand' => 'NEC', 'model' => 'NP43ZL', 'description' => 'Lente ultra largo 5.3-10.7:1', 'cost_price' => 885.00, 'stock' => 2],
            
            // ViewSonic Lentes
            ['name' => 'Lente ViewSonic LEN-009', 'type' => 'Lente', 'brand' => 'ViewSonic', 'model' => 'LEN-009', 'description' => 'Lente ultra short throw 0.49:1 fijo', 'cost_price' => 485.00, 'stock' => 4],
            ['name' => 'Lente ViewSonic LEN-008', 'type' => 'Lente', 'brand' => 'ViewSonic', 'model' => 'LEN-008', 'description' => 'Lente short throw 0.81:1 fijo', 'cost_price' => 365.00, 'stock' => 5],
        ];

        // Combinar todas las piezas
        $allParts = array_merge($printerParts, $projectorParts);

        // Crear las piezas con distribución de proveedores
        $providerIndex = 0;
        foreach ($allParts as $part) {
            // Rotar entre proveedores para variedad
            $provider = $providers[$providerIndex % $providers->count()];
            $providerIndex++;
            
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

        // Conteo por tipo de pieza
        $cabezales = count(array_filter($printerParts, fn($p) => $p['type'] === 'Cabezal de Impresión'));
        $rodillos = count(array_filter($printerParts, fn($p) => $p['type'] === 'Rodillo'));
        $fusores = count(array_filter($printerParts, fn($p) => $p['type'] === 'Fusor'));
        $lamparas = count(array_filter($projectorParts, fn($p) => $p['type'] === 'Lámpara de Proyector'));
        $lentes = count(array_filter($projectorParts, fn($p) => $p['type'] === 'Lente'));

        $this->command->info("✅ Piezas de repuesto creadas correctamente:");
        $this->command->newLine();
        $this->command->info("   📄 IMPRESORAS ({$printerCount} piezas):");
        $this->command->info("      • Cabezales de Impresión: {$cabezales}");
        $this->command->info("      • Rodillos: {$rodillos}");
        $this->command->info("      • Fusores: {$fusores}");
        $this->command->newLine();
        $this->command->info("   📽️  PROYECTORES ({$projectorCount} piezas):");
        $this->command->info("      • Lámparas: {$lamparas}");
        $this->command->info("      • Lentes: {$lentes}");
        $this->command->newLine();
        $this->command->info("   📦 TOTAL: {$total} piezas de repuesto");
        $this->command->info("   🏭 Distribuidas entre {$providers->count()} proveedores activos");
    }
}
