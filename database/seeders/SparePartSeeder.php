<?php

namespace Database\Seeders;

use App\Models\SparePart;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spareParts = [
            // ===== REPUESTOS PARA IMPRESORAS =====
            
            // Cabezales HP
            ['type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL Negro', 'part_number' => 'L0S58AN', 'description' => 'Cabezal de impresión negro de alto rendimiento para OfficeJet Pro 8210/8710'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL Cian', 'part_number' => 'F6U16AN', 'description' => 'Cabezal de impresión cian de alto rendimiento'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL Magenta', 'part_number' => 'F6U17AN', 'description' => 'Cabezal de impresión magenta de alto rendimiento'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '953XL Amarillo', 'part_number' => 'F6U18AN', 'description' => 'Cabezal de impresión amarillo de alto rendimiento'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'HP', 'model' => '934XL Negro', 'part_number' => 'C2P23AN', 'description' => 'Cabezal negro extra capacidad para OfficeJet Pro 6230/6830'],
            
            // Cabezales Canon
            ['type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PG-245XL Negro', 'part_number' => '8278B001', 'description' => 'Cabezal negro de alta capacidad para Pixma MG/MX/IP series'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'CL-246XL Tricolor', 'part_number' => '8280B001', 'description' => 'Cabezal tricolor de alta capacidad para Pixma'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'PGI-250XL Negro', 'part_number' => '6432B001', 'description' => 'Cabezal pigmento negro XL para Pixma MG/MX/IP'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Canon', 'model' => 'CLI-251 CMYK', 'part_number' => '6513B009', 'description' => 'Set completo CMYK para Pixma MG/MX/IP'],
            
            // Cabezales Epson
            ['type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664 Negro', 'part_number' => 'T664120-AL', 'description' => 'Botella de tinta negra para EcoTank L395/L495/L575'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664 Cian', 'part_number' => 'T664220-AL', 'description' => 'Botella de tinta cian para EcoTank'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664 Magenta', 'part_number' => 'T664320-AL', 'description' => 'Botella de tinta magenta para EcoTank'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => 'T664 Amarillo', 'part_number' => 'T664420-AL', 'description' => 'Botella de tinta amarilla para EcoTank'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Epson', 'model' => '103 CMYK', 'part_number' => 'T103120-AL', 'description' => 'Set completo para L3110/L3150'],
            
            // Cabezales Brother
            ['type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013 Negro', 'part_number' => 'LC3013BK', 'description' => 'Cabezal ultra alta capacidad negro para MFC-J491DW/J497DW'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013 Cian', 'part_number' => 'LC3013C', 'description' => 'Cabezal ultra alta capacidad cian'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013 Magenta', 'part_number' => 'LC3013M', 'description' => 'Cabezal ultra alta capacidad magenta'],
            ['type' => 'Cabezal de Impresión', 'brand' => 'Brother', 'model' => 'LC3013 Amarillo', 'part_number' => 'LC3013Y', 'description' => 'Cabezal ultra alta capacidad amarillo'],
            
            // Rodillos para impresoras
            ['type' => 'Rodillo', 'brand' => 'HP', 'model' => 'LaserJet M454 Transfer', 'part_number' => 'W1B44A', 'description' => 'Rodillo de transferencia para LaserJet Enterprise M454/M479'],
            ['type' => 'Rodillo', 'brand' => 'HP', 'model' => 'LaserJet M402 Pickup', 'part_number' => 'RM2-5452', 'description' => 'Rodillo de alimentación pickup roller para M402/M426'],
            ['type' => 'Rodillo', 'brand' => 'HP', 'model' => 'LaserJet M402 Separation', 'part_number' => 'RM2-5397', 'description' => 'Rodillo separador de papel para M402/M426'],
            ['type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'IR-ADV Fuser', 'part_number' => 'FM4-8400-000', 'description' => 'Rodillo presión fusor para imageRUNNER ADVANCE'],
            ['type' => 'Rodillo', 'brand' => 'Canon', 'model' => 'IR2520 Pickup', 'part_number' => 'FC6-7083-000', 'description' => 'Rodillo alimentación para imageRUNNER 2520/2525/2530'],
            ['type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'HL-L8360 Pickup', 'part_number' => 'LY9969001', 'description' => 'Rodillo pick-up para HL-L8360CDW/MFC-L8900CDW'],
            ['type' => 'Rodillo', 'brand' => 'Brother', 'model' => 'DR730 Drum', 'part_number' => 'DR730', 'description' => 'Unidad de tambor completa para HL-L2350/L2370/L2390/L2395'],
            ['type' => 'Rodillo', 'brand' => 'Xerox', 'model' => 'WorkCentre 7835 Transfer', 'part_number' => '008R13064', 'description' => 'Rodillo transfer belt assembly para WorkCentre 7830/7835/7845'],
            ['type' => 'Rodillo', 'brand' => 'Ricoh', 'model' => 'MP C3004 Transfer', 'part_number' => 'D1862251', 'description' => 'Rodillo transferencia intermediaria para MP C3004/C3504'],
            
            // Fusores para impresoras
            ['type' => 'Fusor', 'brand' => 'HP', 'model' => 'LaserJet M454 Fuser', 'part_number' => 'W1B43A', 'description' => 'Unidad fusora completa 110V para Color LaserJet Enterprise M454'],
            ['type' => 'Fusor', 'brand' => 'HP', 'model' => 'LaserJet M479 Fuser', 'part_number' => 'W1B44-67901', 'description' => 'Fuser kit Pro MFP M479 110V'],
            ['type' => 'Fusor', 'brand' => 'HP', 'model' => 'LaserJet M607 Fuser', 'part_number' => 'J8J88-67901', 'description' => 'Maintenance kit fusor 110V para M607/M608/M609'],
            ['type' => 'Fusor', 'brand' => 'HP', 'model' => 'LaserJet M402 Fuser', 'part_number' => 'RM2-5425', 'description' => 'Fuser assembly 110V para M402/M426'],
            ['type' => 'Fusor', 'brand' => 'Canon', 'model' => 'IR-ADV C5540 Fixing', 'part_number' => 'FM4-9351-000', 'description' => 'Fixing unit para imageRUNNER ADVANCE C5540/C5550/C5560'],
            ['type' => 'Fusor', 'brand' => 'Canon', 'model' => 'IR2520 Fuser', 'part_number' => 'FM3-9363-000', 'description' => 'Unidad fusora para imageRUNNER 2520/2525/2530'],
            ['type' => 'Fusor', 'brand' => 'Canon', 'model' => 'MF440 Fuser', 'part_number' => 'FM4-4207-000', 'description' => 'Fuser unit para imageCLASS MF440/MF445/MF449'],
            ['type' => 'Fusor', 'brand' => 'Brother', 'model' => 'HL-L8360CDW Fuser', 'part_number' => 'LY7748001', 'description' => 'Fuser unit 100k páginas para HL-L8360CDW'],
            ['type' => 'Fusor', 'brand' => 'Brother', 'model' => 'MFC-L8900CDW Fuser', 'part_number' => 'LU9215001', 'description' => 'Fuser assembly para MFC-L8900CDW/L9570CDW'],
            ['type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'WorkCentre 7835 Fuser', 'part_number' => '008R13040', 'description' => 'Fuser module assembly para WorkCentre 7830/7835/7845/7855'],
            ['type' => 'Fusor', 'brand' => 'Xerox', 'model' => 'Phaser 6510 Fuser', 'part_number' => '108R01481', 'description' => 'Fuser cartridge para Phaser 6510/WorkCentre 6515'],
            ['type' => 'Fusor', 'brand' => 'Ricoh', 'model' => 'MP C3004 Fuser', 'part_number' => 'D1862203', 'description' => 'Fusor original Ricoh para MP C3004/C3504'],
            
            // ===== REPUESTOS PARA PROYECTORES =====
            
            // Lámparas Epson
            ['type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP96', 'part_number' => 'V13H010L96', 'description' => 'Lámpara UHE 380W para EB-2250U/2255U/2265U, vida útil 4000h'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP88', 'part_number' => 'V13H010L88', 'description' => 'Lámpara para PowerLite 955WH/97/98/99W/X27/W29'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP89', 'part_number' => 'V13H010L89', 'description' => 'Lámpara para PowerLite 5350/5300 series'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP95', 'part_number' => 'V13H010L95', 'description' => 'Lámpara para EB-2155W/2165W/2245U'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP87', 'part_number' => 'V13H010L87', 'description' => 'Lámpara para PowerLite 520/525W/530/535W'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Epson', 'model' => 'ELPLP97', 'part_number' => 'V13H010L97', 'description' => 'Lámpara para EB-X49/W49/U42'],
            
            // Lámparas Sony
            ['type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-E212', 'part_number' => 'LMP-E212', 'description' => 'Lámpara para VPL-EX255/EX275/SW536/SW631'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-D214', 'part_number' => 'LMP-D214', 'description' => 'Lámpara para VPL-DX145/DX147'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-E211', 'part_number' => 'LMP-E211', 'description' => 'Lámpara para VPL-EX100/EX120/EX145/EX175'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Sony', 'model' => 'LMP-D213', 'part_number' => 'LMP-D213', 'description' => 'Lámpara para VPL-DW120/DW125/DX120/DX140'],
            
            // Lámparas Panasonic
            ['type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAL500', 'part_number' => 'ET-LAL500', 'description' => 'Lámpara para PT-LB360/LB382/LW312'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAV400', 'part_number' => 'ET-LAV400', 'description' => 'Lámpara para PT-VW530/VW535N/VX605N'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAE300', 'part_number' => 'ET-LAE300', 'description' => 'Lámpara para PT-EW730/EX800'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Panasonic', 'model' => 'ET-LAV200', 'part_number' => 'ET-LAV200', 'description' => 'Lámpara para PT-VW435N/VW430/VX505N'],
            
            // Lámparas BenQ
            ['type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.JGP05.001', 'part_number' => '5J.JGP05.001', 'description' => 'Lámpara para MH534A/MS535A/MW535A'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.J9H05.001', 'part_number' => '5J.J9H05.001', 'description' => 'Lámpara para W1070+/W1080ST+'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.JEE05.001', 'part_number' => '5J.JEE05.001', 'description' => 'Lámpara para HT2150ST/W1110'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'BenQ', 'model' => '5J.J7L05.001', 'part_number' => '5J.J7L05.001', 'description' => 'Lámpara para W1070/W1080ST'],
            
            // Lámparas Optoma
            ['type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'BL-FP280I', 'part_number' => 'BL-FP280I', 'description' => 'Lámpara para EH412/W412/X412, 3500h'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'BL-FP190E', 'part_number' => 'BL-FP190E', 'description' => 'Lámpara para HD141X/HD26/GT1080'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'Optoma', 'model' => 'SP.71P01GC01', 'part_number' => 'SP.71P01GC01', 'description' => 'Lámpara para HD20/HD200X/HD20-LV'],
            
            // Lámparas ViewSonic
            ['type' => 'Lámpara de Proyector', 'brand' => 'ViewSonic', 'model' => 'RLC-118', 'part_number' => 'RLC-118', 'description' => 'Lámpara para PA503S/PG603X, 3500h'],
            ['type' => 'Lámpara de Proyector', 'brand' => 'ViewSonic', 'model' => 'RLC-117', 'part_number' => 'RLC-117', 'description' => 'Lámpara para PA503W/PG703W'],
            
            // Lentes para proyectores
            ['type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLM15', 'part_number' => 'V12H004M15', 'description' => 'Lente medio zoom 2.1-3.7:1 para EB-G series'],
            ['type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLU03', 'part_number' => 'V12H004U03', 'description' => 'Lente ultra corto alcance 0.35:1 para EB series'],
            ['type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLM11', 'part_number' => 'V12H004M11', 'description' => 'Lente largo alcance 6.0-9.0:1 para EB-G/L series'],
            ['type' => 'Lente', 'brand' => 'Epson', 'model' => 'ELPLW08', 'part_number' => 'V12H004W08', 'description' => 'Lente gran angular 1.3-1.7:1 para EB-G/L series'],
            
            ['type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z3032', 'part_number' => 'VPLL-Z3032', 'description' => 'Lente corto alcance 0.85-1.02:1 para VPL-F series'],
            ['type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z4045', 'part_number' => 'VPLL-Z4045', 'description' => 'Lente medio zoom 1.85-2.73:1 para VPL-F series'],
            ['type' => 'Lente', 'brand' => 'Sony', 'model' => 'VPLL-Z4111', 'part_number' => 'VPLL-Z4111', 'description' => 'Lente largo zoom 5.0-8.0:1 para VPL-F series'],
            
            ['type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE250', 'part_number' => 'ET-DLE250', 'description' => 'Lente zoom 2.4-3.7:1 para PT-D/DZ series'],
            ['type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE150', 'part_number' => 'ET-DLE150', 'description' => 'Lente zoom 1.4-2.2:1 para PT-D/DZ series'],
            ['type' => 'Lente', 'brand' => 'Panasonic', 'model' => 'ET-DLE450', 'part_number' => 'ET-DLE450', 'description' => 'Lente largo alcance 4.5-7.3:1 para PT-D/DZ series'],
            
            ['type' => 'Lente', 'brand' => 'BenQ', 'model' => 'LS2ST2', 'part_number' => 'LS2ST2', 'description' => 'Lente short throw 0.78:1 fijo para LK/LU series'],
            ['type' => 'Lente', 'brand' => 'BenQ', 'model' => 'LS2LT2', 'part_number' => 'LS2LT2', 'description' => 'Lente largo throw 2.0-3.0:1 para LK/LU series'],
            
            ['type' => 'Lente', 'brand' => 'Optoma', 'model' => 'BX-DL100', 'part_number' => 'BX-DL100', 'description' => 'Lente largo alcance 3.16-5.05:1 para ZU/ZH series'],
            ['type' => 'Lente', 'brand' => 'Optoma', 'model' => 'BX-CTA01', 'part_number' => 'BX-CTA01', 'description' => 'Lente short throw 0.65-0.76:1 para ZU/ZH series'],
            
            ['type' => 'Lente', 'brand' => 'ViewSonic', 'model' => 'LEN-009', 'part_number' => 'LEN-009', 'description' => 'Lente ultra short throw 0.49:1 fijo para LS series'],
            ['type' => 'Lente', 'brand' => 'ViewSonic', 'model' => 'LEN-008', 'part_number' => 'LEN-008', 'description' => 'Lente short throw 0.81:1 fijo para LS series'],
        ];

        foreach ($spareParts as $partData) {
            SparePart::firstOrCreate(
                [
                    'brand' => $partData['brand'],
                    'model' => $partData['model'],
                    'type' => $partData['type'],
                ],
                [
                    'part_number' => $partData['part_number'] ?? null,
                    'description' => $partData['description'] ?? null,
                ]
            );
        }

        $printerParts = count(array_filter($spareParts, fn($p) => in_array($p['type'], ['Cabezal de Impresión', 'Rodillo', 'Fusor'])));
        $projectorParts = count(array_filter($spareParts, fn($p) => in_array($p['type'], ['Lámpara de Proyector', 'Lente'])));
        
        $this->command->info("✅ Catálogo de Repuestos creado correctamente:");
        $this->command->info("   📄 Repuestos para IMPRESORAS: {$printerParts}");
        $this->command->info("   📽️  Repuestos para PROYECTORES: {$projectorParts}");
        $this->command->info("   📦 TOTAL: " . count($spareParts) . " modelos en catálogo");
    }
}
