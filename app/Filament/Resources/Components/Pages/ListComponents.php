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
                        'CPU',
                        'Motherboard',
                        'GPU',
                        'RAM',
                        'ROM',
                        'PowerSupply',
                        'TowerCase',
                    ])
                    ->where('status', '!=', 'Retirado')
                ),

            'pc_peripherals' => Tab::make('Periféricos')
                ->icon(Heroicon::CursorArrowRays)
                ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereIn('componentable_type', [
                        'Monitor',
                        'Keyboard',
                        'Mouse',
                        'Splitter',
                        'AudioDevice',
                        'NetworkAdapter',
                    ])
                ->where('status', '!=', 'Retirado')
            ),

            'printers' => Tab::make('Impresoras')
                ->icon(Heroicon::Printer)
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('componentable_type', 'SparePart')
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
                    ->where('componentable_type', 'SparePart')
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
                            'CPU',
                            'Motherboard',
                            'GPU',
                            'RAM',
                            'ROM',
                            'PowerSupply',
                            'NetworkAdapter',
                            'TowerCase',
                            'Monitor',
                            'Keyboard',
                            'Mouse',
                            'Splitter',
                            'AudioDevice',
                        ])
                        ->where(function ($subQ) {
                            $subQ->where('componentable_type', '!=', 'SparePart')
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
