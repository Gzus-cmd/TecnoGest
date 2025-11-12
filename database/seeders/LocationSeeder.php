<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            // Pabellón A - Administrativo
            ['pavilion' => 'A', 'apartment' => 101, 'name' => 'Dirección General'],
            ['pavilion' => 'A', 'apartment' => 102, 'name' => 'Secretaría Académica'],
            ['pavilion' => 'A', 'apartment' => 103, 'name' => 'Recursos Humanos'],
            ['pavilion' => 'A', 'apartment' => 104, 'name' => 'Contabilidad y Finanzas'],
            ['pavilion' => 'A', 'apartment' => 105, 'name' => 'Oficina de Soporte TI'],
            ['pavilion' => 'A', 'apartment' => 106, 'name' => 'Sala de Reuniones Principal'],

            // Pabellón B - Aulas
            ['pavilion' => 'B', 'apartment' => 201, 'name' => 'Aula 101'],
            ['pavilion' => 'B', 'apartment' => 202, 'name' => 'Aula 102'],
            ['pavilion' => 'B', 'apartment' => 203, 'name' => 'Aula 103'],
            ['pavilion' => 'B', 'apartment' => 204, 'name' => 'Aula 104'],
            ['pavilion' => 'B', 'apartment' => 205, 'name' => 'Aula 105'],
            ['pavilion' => 'B', 'apartment' => 206, 'name' => 'Aula Multimedia'],
            ['pavilion' => 'B', 'apartment' => 207, 'name' => 'Aula de Conferencias'],

            // Pabellón C - Laboratorios
            ['pavilion' => 'C', 'apartment' => 301, 'name' => 'Laboratorio de Informática 1'],
            ['pavilion' => 'C', 'apartment' => 302, 'name' => 'Laboratorio de Informática 2'],
            ['pavilion' => 'C', 'apartment' => 303, 'name' => 'Laboratorio de Informática 3'],
            ['pavilion' => 'C', 'apartment' => 304, 'name' => 'Laboratorio de Redes'],
            ['pavilion' => 'C', 'apartment' => 305, 'name' => 'Laboratorio de Programación'],
            ['pavilion' => 'C', 'apartment' => 306, 'name' => 'Laboratorio de Hardware'],
            ['pavilion' => 'C', 'apartment' => 307, 'name' => 'Taller de Mantenimiento'],

            // Pabellón D - Biblioteca y Servicios
            ['pavilion' => 'D', 'apartment' => 401, 'name' => 'Biblioteca Central'],
            ['pavilion' => 'D', 'apartment' => 402, 'name' => 'Sala de Lectura'],
            ['pavilion' => 'D', 'apartment' => 403, 'name' => 'Centro de Fotocopiado'],
            ['pavilion' => 'D', 'apartment' => 404, 'name' => 'Centro de Cómputo Estudiantes'],
            ['pavilion' => 'D', 'apartment' => 405, 'name' => 'Sala de Investigación'],

            // Pabellón E - Infraestructura TI
            ['pavilion' => 'E', 'apartment' => 501, 'name' => 'Sala de Servidores Principal'],
            ['pavilion' => 'E', 'apartment' => 502, 'name' => 'Centro de Datos'],
            ['pavilion' => 'E', 'apartment' => 503, 'name' => 'UPS y Energía'],
            ['pavilion' => 'E', 'apartment' => 504, 'name' => 'Almacén de Equipos'],
            ['pavilion' => 'E', 'apartment' => 505, 'name' => 'Taller de Reparaciones'],
            ['pavilion' => 'E', 'apartment' => 506, 'name' => 'Cuarto de Telecomunicaciones'],
            ['pavilion' => 'E', 'apartment' => 507, 'name' => 'Sala de Informática'], // Para traslados de mantenimiento

            // Pabellón F - Otros
            ['pavilion' => 'F', 'apartment' => 601, 'name' => 'Auditorio'],
            ['pavilion' => 'F', 'apartment' => 602, 'name' => 'Cafetería'],
            ['pavilion' => 'F', 'apartment' => 603, 'name' => 'Sala de Docentes'],
            ['pavilion' => 'F', 'apartment' => 604, 'name' => 'Oficina de Admisión'],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(
                [
                    'pavilion' => $location['pavilion'],
                    'apartment' => $location['apartment']
                ],
                $location
            );
        }

        $count = Location::count();
        $pavilions = Location::distinct('pavilion')->count('pavilion');
        
        $this->command->info("✅ Ubicaciones creadas: {$count} ubicaciones en {$pavilions} pabellones");
    }
}
