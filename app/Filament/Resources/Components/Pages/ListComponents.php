<?php

namespace App\Filament\Resources\Components\Pages;

use App\Filament\Resources\Components\ComponentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListComponents extends ListRecords
{
    protected static string $resource = ComponentResource::class;

    protected static ?string $title = 'Lista de Componentes';


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Componente'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon(Heroicon::ArchiveBox)
                ,


            'pc_hardware' => Tab::make('Hardware')
                ->icon(Heroicon::ComputerDesktop)
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereIn('componentable_type', [
                        \App\Models\CPU::class,
                        \App\Models\Motherboard::class,
                        \App\Models\GPU::class,
                        \App\Models\RAM::class,
                        \App\Models\ROM::class,
                        \App\Models\PowerSupply::class,
                        \App\Models\TowerCase::class,
                    ])
                    ->where('status', '!=', 'Retirado')
                ),

            'pc_peripherals' => Tab::make('Periféricos')
                ->icon(Heroicon::CursorArrowRays)
                ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereIn('componentable_type', [
                        \App\Models\Monitor::class,
                        \App\Models\Keyboard::class,
                        \App\Models\Mouse::class,
                        \App\Models\Splitter::class,
                        \App\Models\AudioDevice::class,
                        \App\Models\NetworkAdapter::class,
                    ])
                ->where('status', '!=', 'Retirado')
            ),

            'printers' => Tab::make('Impresoras')
                ->icon(Heroicon::Printer)
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('componentable_type', \App\Models\SparePart::class)
                    ->whereExists(function ($q) {
                        $q->select(DB::raw(1))
                          ->from('spare_parts')
                          ->whereColumn('spare_parts.id', 'components.componentable_id')
                          ->whereIn('spare_parts.type', ['Cabezal de Impresión', 'Rodillo', 'Fusor']);
                    })
                    ->where('status', '!=', 'Retirado')
                ),

            'projectors' => Tab::make('Proyectores')
                ->icon(Heroicon::VideoCamera)
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('componentable_type', \App\Models\SparePart::class)
                    ->whereExists(function ($q) {
                        $q->select(DB::raw(1))
                          ->from('spare_parts')
                          ->whereColumn('spare_parts.id', 'components.componentable_id')
                          ->whereIn('spare_parts.type', ['Lámpara de Proyector', 'Lente']);
                    })
                    ->where('status', '!=', 'Retirado')
                ),

            'others' => Tab::make('Otros')
                ->icon(Heroicon::WrenchScrewdriver)
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where(function ($q) {
                        $q->whereNotIn('componentable_type', [
                            \App\Models\CPU::class,
                            \App\Models\Motherboard::class,
                            \App\Models\GPU::class,
                            \App\Models\RAM::class,
                            \App\Models\ROM::class,
                            \App\Models\PowerSupply::class,
                            \App\Models\NetworkAdapter::class,
                            \App\Models\TowerCase::class,
                            \App\Models\Monitor::class,
                            \App\Models\Keyboard::class,
                            \App\Models\Mouse::class,
                            \App\Models\Splitter::class,
                            \App\Models\AudioDevice::class,
                        ])
                        ->where(function ($subQ) {
                            $subQ->where('componentable_type', '!=', \App\Models\SparePart::class)
                                ->orWhereExists(function ($spareQ) {
                                    $spareQ->select(DB::raw(1))
                                          ->from('spare_parts')
                                          ->whereColumn('spare_parts.id', 'components.componentable_id')
                                          ->whereNotIn('spare_parts.type', [
                                              'Cabezal de Impresión', 
                                              'Rodillo', 
                                              'Fusor',
                                              'Lámpara de Proyector', 
                                              'Lente'
                                          ]);
                                });
                        });
                    })
                    ->where('status', '!=', 'Retirado')
                ),


            'inactive' => Tab::make('Retirados')
                ->icon(Heroicon::ArchiveBoxXMark)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Retirado')),
        ];
    }
}
