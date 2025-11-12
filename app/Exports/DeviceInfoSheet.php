<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class DeviceInfoSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $device;
    protected $type;

    public function __construct($device, string $type)
    {
        $this->device = $device;
        $this->type = $type;
    }

    public function collection()
    {
        $data = new Collection();

        // Información básica del dispositivo
        $data->push(['Campo', 'Valor']);
        $data->push(['Serial', $this->device->serial]);
        $data->push(['Estado', $this->device->status]);
        
        if ($this->type === 'computer') {
            $this->device->load(['os', 'location', 'components.componentable']);
            
            $data->push(['Ubicación', $this->device->location ? $this->device->location->pavilion . ' - ' . $this->device->location->name : 'No asignada']);
            $data->push(['Dirección IP', $this->device->ip_address ?? 'No asignada']);
            $data->push(['', '']);
            $data->push(['SISTEMA OPERATIVO', '']);
            $data->push(['Nombre', $this->device->os->name ?? 'N/A']);
            $data->push(['Versión', $this->device->os->version ?? 'N/A']);
            $data->push(['Arquitectura', $this->device->os->architecture ?? 'N/A']);
            $data->push(['Licencia', $this->device->os->license_key ?? 'N/A']);
            $data->push(['', '']);
            $data->push(['COMPONENTES ACTUALES', '']);
            
            // Agregar componentes
            $this->addComputerComponents($data);
            
        } elseif ($this->type === 'printer') {
            $this->device->load(['printerModel', 'location', 'components.componentable']);
            
            $data->push(['Ubicación', $this->device->location ? $this->device->location->pavilion . ' - ' . $this->device->location->name : 'No asignada']);
            $data->push(['Dirección IP', $this->device->ip_address ?? 'No asignada']);
            $data->push(['', '']);
            $data->push(['MODELO DE IMPRESORA', '']);
            $data->push(['Marca', $this->device->printerModel->brand ?? 'N/A']);
            $data->push(['Modelo', $this->device->printerModel->model ?? 'N/A']);
            $data->push(['Tipo', $this->device->printerModel->type ?? 'N/A']);
            $data->push(['', '']);
            $data->push(['COMPONENTES ACTUALES', '']);
            
            // Agregar componentes de impresora (si aplica)
            $this->addPrinterComponents($data);
            
        } elseif ($this->type === 'projector') {
            $this->device->load(['projectorModel', 'location', 'components.componentable']);
            
            $data->push(['Ubicación', $this->device->location ? $this->device->location->pavilion . ' - ' . $this->device->location->name : 'No asignada']);
            $data->push(['', '']);
            $data->push(['MODELO DE PROYECTOR', '']);
            $data->push(['Marca', $this->device->projectorModel->brand ?? 'N/A']);
            $data->push(['Modelo', $this->device->projectorModel->model ?? 'N/A']);
            $data->push(['Resolución', $this->device->projectorModel->resolution ?? 'N/A']);
            $data->push(['Brillo (Lúmenes)', $this->device->projectorModel->brightness ?? 'N/A']);
            $data->push(['', '']);
            $data->push(['COMPONENTES ACTUALES', '']);
            
            // Agregar componentes de proyector (si aplica)
            $this->addProjectorComponents($data);
        }

        return $data;
    }

    protected function addComputerComponents(Collection $data)
    {
        $componentTypes = [
            'App\Models\Motherboard' => 'Placa Base',
            'App\Models\CPU' => 'Procesador',
            'App\Models\GPU' => 'Tarjeta Gráfica',
            'App\Models\PowerSupply' => 'Fuente de Poder',
            'App\Models\TowerCase' => 'Gabinete',
            'App\Models\NetworkAdapter' => 'Adaptador de Red',
        ];

        foreach ($componentTypes as $type => $label) {
            $component = $this->device->components->firstWhere('componentable_type', $type);
            if ($component && $component->componentable) {
                $comp = $component->componentable;
                $data->push([$label, "{$comp->brand} {$comp->model} - Serial: {$component->serial}"]);
            }
        }

        // RAMs
        $rams = $this->device->components->where('componentable_type', 'App\Models\RAM');
        foreach ($rams as $index => $ram) {
            if ($ram->componentable) {
                $r = $ram->componentable;
                $data->push(["RAM " . ($index + 1), "{$r->brand} {$r->model} - {$r->capacity}GB - Serial: {$ram->serial}"]);
            }
        }

        // ROMs
        $roms = $this->device->components->where('componentable_type', 'App\Models\ROM');
        foreach ($roms as $index => $rom) {
            if ($rom->componentable) {
                $r = $rom->componentable;
                $data->push(["Almacenamiento " . ($index + 1), "{$r->brand} {$r->model} - {$r->capacity}GB - Serial: {$rom->serial}"]);
            }
        }

        // Monitores
        $monitors = $this->device->components->where('componentable_type', 'App\Models\Monitor');
        foreach ($monitors as $index => $monitor) {
            if ($monitor->componentable) {
                $m = $monitor->componentable;
                $data->push(["Monitor " . ($index + 1), "{$m->brand} {$m->model} - {$m->screen_size}\" - Serial: {$monitor->serial}"]);
            }
        }

        // Periféricos
        $peripherals = [
            'App\Models\Keyboard' => 'Teclado',
            'App\Models\Mouse' => 'Mouse',
            'App\Models\AudioDevice' => 'Audio',
            'App\Models\Stabilizer' => 'Estabilizador',
            'App\Models\Splitter' => 'Multicontacto',
        ];

        foreach ($peripherals as $type => $label) {
            $component = $this->device->components->firstWhere('componentable_type', $type);
            if ($component && $component->componentable) {
                $comp = $component->componentable;
                $data->push([$label, "{$comp->brand} {$comp->model} - Serial: {$component->serial}"]);
            }
        }
    }

    protected function addPrinterComponents(Collection $data)
    {
        // Las impresoras pueden tener estabilizadores o splitters
        $componentTypes = [
            'App\Models\Stabilizer' => 'Estabilizador',
            'App\Models\Splitter' => 'Multicontacto',
        ];

        foreach ($componentTypes as $type => $label) {
            $component = $this->device->components->firstWhere('componentable_type', $type);
            if ($component && $component->componentable) {
                $comp = $component->componentable;
                $data->push([$label, "{$comp->brand} {$comp->model} - Serial: {$component->serial}"]);
            }
        }
    }

    protected function addProjectorComponents(Collection $data)
    {
        // Los proyectores pueden tener estabilizadores o splitters
        $componentTypes = [
            'App\Models\Stabilizer' => 'Estabilizador',
            'App\Models\Splitter' => 'Multicontacto',
        ];

        foreach ($componentTypes as $type => $label) {
            $component = $this->device->components->firstWhere('componentable_type', $type);
            if ($component && $component->componentable) {
                $comp = $component->componentable;
                $data->push([$label, "{$comp->brand} {$comp->model} - Serial: {$component->serial}"]);
            }
        }
    }

    public function title(): string
    {
        return 'Información del Sistema';
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 70,
        ];
    }
}
