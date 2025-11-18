<?php

namespace App\Filament\Resources\Computers\Tables;

use App\Models\Component;
use App\Models\OS;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;

class ComputersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial')
                    ->label('Codigo')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->label('Departamento')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Inactivo' => 'gray',
                        'En Mantenimiento' => 'warning',
                        'Activo' => 'success',
                        'Desmantelado' => 'danger',
                    }),
                TextColumn::make('ip_address')
                    ->label('Dirección IP')
                    ->searchable(),
                TextColumn::make('os.name')
                    ->label('Sistema Operativo')
                    ->searchable(),
                TextColumn::make('peripheral.code')
                    ->label('Periféricos')
                    ->badge()
                    ->color('info')
                    ->default('Sin periféricos')
                    ->formatStateUsing(fn ($state) => $state ?? 'Sin periféricos'),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'Inactivo' => 'Inactivo',
                        'En Mantenimiento' => 'En Mantenimiento',
                        'Activo' => 'Activo',
                        'Desmantelado' => 'Desmantelado',
                    ]),

                SelectFilter::make('location.name')
                    ->label('Departamento')
                    ->relationship('location', 'name')
                    ->multiple()
                    ->searchable()


            ])
            ->recordActions([
                Action::make('verComponentes')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->modalHeading('Detalles de la Computadora')
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->infolist(function ($record) {
                        // Cargar componentes con sus relaciones
                        $record->load(['components.componentable', 'os', 'peripheral.components.componentable']);
                        
                        // Componentes internos del CPU
                        $motherboard = $record->components->firstWhere('componentable_type', 'App\Models\Motherboard');
                        $cpu = $record->components->firstWhere('componentable_type', 'App\Models\CPU');
                        $gpu = $record->components->firstWhere('componentable_type', 'App\Models\GPU');
                        $powerSupply = $record->components->firstWhere('componentable_type', 'App\Models\PowerSupply');
                        $towerCase = $record->components->firstWhere('componentable_type', 'App\Models\TowerCase');
                        $networkAdapter = $record->components->firstWhere('componentable_type', 'App\Models\NetworkAdapter');
                        $rams = $record->components->where('componentable_type', 'App\Models\RAM');
                        $roms = $record->components->where('componentable_type', 'App\Models\ROM');
                        
                        // Componentes periféricos (ahora desde peripheral)
                        $peripheral = $record->peripheral;
                        $keyboard = $peripheral?->components->firstWhere('componentable_type', 'App\Models\Keyboard');
                        $mouse = $peripheral?->components->firstWhere('componentable_type', 'App\Models\Mouse');
                        $audioDevice = $peripheral?->components->firstWhere('componentable_type', 'App\Models\AudioDevice');
                        $stabilizer = $peripheral?->components->firstWhere('componentable_type', 'App\Models\Stabilizer');
                        $splitter = $peripheral?->components->firstWhere('componentable_type', 'App\Models\Splitter');
                        $monitors = $peripheral?->components->where('componentable_type', 'App\Models\Monitor') ?? collect();

                        return [
                            // SECCIÓN: SOFTWARE Y RED
                            ViewEntry::make('software_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '💻 Software y Red',
                                    'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),
                            
                            Section::make()
                                ->schema([
                                    TextEntry::make('os_info')
                                        ->label('Sistema Operativo')
                                        ->state(function () use ($record) {
                                            if (!$record->os) return 'No asignado';
                                            $os = $record->os;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Nombre:</span> <span style='color: #9ca3af;'>{$os->name}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Versión:</span> <span style='color: #9ca3af;'>" . ($os->version ?? 'N/A') . "</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Arquitectura:</span> <span style='color: #9ca3af;'>" . ($os->architecture ?? 'N/A') . "</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Licencia:</span> <span style='color: #9ca3af;'>" . ($os->license_key ?? 'N/A') . "</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('ip_address')
                                        ->label('Dirección IP')
                                        ->state(function () use ($record) {
                                            return "<span style='color: #9ca3af;'>" . ($record->ip_address ?? 'No asignada') . "</span>";
                                        })
                                        ->html(),
                                    
                                    TextEntry::make('peripheral_info')
                                        ->label('Periféricos Asignados')
                                        ->state(function () use ($peripheral) {
                                            if (!$peripheral) return "<span style='color: #9ca3af;'>Sin periféricos asignados</span>";
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div><span style='font-weight: 700; color: #10b981;'>Código:</span> <span style='color: #9ca3af;'>{$peripheral->code}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$peripheral->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(2),

                            // SECCIÓN: HARDWARE PRINCIPAL
                            ViewEntry::make('hardware_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '🔧 Hardware Principal',
                                    'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),
                            
                            Section::make()
                                ->schema([
                                    TextEntry::make('motherboard_info')
                                        ->label('Placa Base')
                                        ->state(function () use ($motherboard) {
                                            if (!$motherboard) return '<span style="color: #9ca3af;">No asignada</span>';
                                            $mb = $motherboard->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$mb->brand} {$mb->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Socket:</span> <span style='color: #9ca3af;'>{$mb->socket}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$motherboard->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$motherboard->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('cpu_info')
                                        ->label('Procesador (CPU)')
                                        ->state(function () use ($cpu) {
                                            if (!$cpu) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $c = $cpu->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$c->brand} {$c->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Socket:</span> <span style='color: #9ca3af;'>{$c->socket}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Frecuencia:</span> <span style='color: #9ca3af;'>{$c->frequency} GHz</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Núcleos:</span> <span style='color: #9ca3af;'>{$c->cores}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$cpu->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$cpu->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('gpu_info')
                                        ->label('Tarjeta Gráfica (GPU)')
                                        ->state(function () use ($gpu) {
                                            if (!$gpu) return '<span style="color: #9ca3af;">No asignada</span>';
                                            $g = $gpu->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$g->brand} {$g->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>VRAM:</span> <span style='color: #9ca3af;'>{$g->vram} GB</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$gpu->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$gpu->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('power_supply_info')
                                        ->label('Fuente de Poder')
                                        ->state(function () use ($powerSupply) {
                                            if (!$powerSupply) return '<span style="color: #9ca3af;">No asignada</span>';
                                            $ps = $powerSupply->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$ps->brand} {$ps->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Potencia:</span> <span style='color: #9ca3af;'>{$ps->power} W</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$powerSupply->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$powerSupply->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('tower_case_info')
                                        ->label('Gabinete/Case')
                                        ->state(function () use ($towerCase) {
                                            if (!$towerCase) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $tc = $towerCase->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$tc->brand} {$tc->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$towerCase->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$towerCase->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('network_adapter_info')
                                        ->label('Adaptador de Red')
                                        ->state(function () use ($networkAdapter) {
                                            if (!$networkAdapter) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $na = $networkAdapter->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$na->brand} {$na->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$networkAdapter->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$networkAdapter->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(3),

                            // SECCIÓN: MEMORIA Y ALMACENAMIENTO
                            ViewEntry::make('memoria_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '💾 Memoria y Almacenamiento',
                                    'gradient' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                                    'textColor' => '#1f2937'
                                ])
                                ->columnSpanFull(),
                            
                            Section::make()
                                ->schema([
                                    TextEntry::make('rams_info')
                                        ->label('Memorias RAM')
                                        ->state(function () use ($rams) {
                                            if ($rams->isEmpty()) return '<span style="color: #9ca3af;">No hay memorias RAM asignadas</span>';
                                            return $rams->map(function ($ram, $index) {
                                                $r = $ram->componentable;
                                                $num = $index + 1;
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid #6366f1;'>" .
                                                       "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>RAM #{$num}: {$r->brand} {$r->model}</div>" .
                                                       "<div style='line-height: 1.6;'>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Capacidad:</span> <span style='color: #9ca3af;'>{$r->capacity} GB</span> | " .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Tipo:</span> <span style='color: #9ca3af;'>{$r->type}</span> | " .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Frecuencia:</span> <span style='color: #9ca3af;'>{$r->frequency} MHz</span><br>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$ram->serial}</span> | " .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$ram->status}</span>" .
                                                       "</div></div>";
                                            })->join('');
                                        })
                                        ->html()
                                        ->columnSpanFull(),

                                    TextEntry::make('roms_info')
                                        ->label('Almacenamiento (ROMs)')
                                        ->state(function () use ($roms) {
                                            if ($roms->isEmpty()) return '<span style="color: #9ca3af;">No hay almacenamiento asignado</span>';
                                            return $roms->map(function ($rom, $index) {
                                                $r = $rom->componentable;
                                                $num = $index + 1;
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid #8b5cf6;'>" .
                                                       "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>Almacenamiento #{$num}: {$r->brand} {$r->model}</div>" .
                                                       "<div style='line-height: 1.6;'>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Capacidad:</span> <span style='color: #9ca3af;'>{$r->capacity} GB</span> | " .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Tipo:</span> <span style='color: #9ca3af;'>{$r->type}</span><br>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$rom->serial}</span> | " .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$rom->status}</span>" .
                                                       "</div></div>";
                                            })->join('');
                                        })
                                        ->html()
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),

                            // SECCIÓN: PERIFÉRICOS
                            ViewEntry::make('perifericos_header')
                                ->view('filament.infolists.section-header', [
                                    'title' => '🖥️ Periféricos',
                                    'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                    'textColor' => 'white'
                                ])
                                ->columnSpanFull(),
                            
                            Section::make()
                                ->schema([
                                    TextEntry::make('monitors_info')
                                        ->label('Monitores')
                                        ->state(function () use ($monitors) {
                                            if ($monitors->isEmpty()) return '<span style="color: #9ca3af;">No hay monitores asignados</span>';
                                            return $monitors->map(function ($monitor, $index) {
                                                $m = $monitor->componentable;
                                                $num = $index + 1;
                                                return "<div style='margin-bottom: 12px; padding-left: 12px; border-left: 3px solid #10b981;'>" .
                                                       "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>Monitor #{$num}: {$m->brand} {$m->model}</div>" .
                                                       "<div style='line-height: 1.6;'>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Tamaño:</span> <span style='color: #9ca3af;'>{$m->screen_size} pulgadas</span><br>" .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$monitor->serial}</span> | " .
                                                       "<span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$monitor->status}</span>" .
                                                       "</div></div>";
                                            })->join('');
                                        })
                                        ->html()
                                        ->columnSpanFull(),

                                    TextEntry::make('keyboard_info')
                                        ->label('Teclado')
                                        ->state(function () use ($keyboard) {
                                            if (!$keyboard) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $kb = $keyboard->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$kb->brand} {$kb->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$keyboard->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$keyboard->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('mouse_info')
                                        ->label('Mouse')
                                        ->state(function () use ($mouse) {
                                            if (!$mouse) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $m = $mouse->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$m->brand} {$m->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$mouse->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$mouse->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('audio_device_info')
                                        ->label('Dispositivo de Audio')
                                        ->state(function () use ($audioDevice) {
                                            if (!$audioDevice) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $ad = $audioDevice->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$ad->brand} {$ad->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$audioDevice->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$audioDevice->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('stabilizer_info')
                                        ->label('Estabilizador')
                                        ->state(function () use ($stabilizer) {
                                            if (!$stabilizer) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $st = $stabilizer->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$st->brand} {$st->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Potencia:</span> <span style='color: #9ca3af;'>{$st->power} VA</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$stabilizer->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$stabilizer->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),

                                    TextEntry::make('splitter_info')
                                        ->label('Multicontacto/Splitter')
                                        ->state(function () use ($splitter) {
                                            if (!$splitter) return '<span style="color: #9ca3af;">No asignado</span>';
                                            $sp = $splitter->componentable;
                                            return "<div style='line-height: 1.8;'>" .
                                                   "<div style='font-weight: 700; color: #f3f4f6; margin-bottom: 8px; font-size: 1.05rem;'>{$sp->brand} {$sp->model}</div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Número de Tomas:</span> <span style='color: #9ca3af;'>{$sp->outlets}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Serial:</span> <span style='color: #9ca3af;'>{$splitter->serial}</span></div>" .
                                                   "<div><span style='font-weight: 400; color: #6b7280;'>Estado:</span> <span style='color: #9ca3af;'>{$splitter->status}</span></div>" .
                                                   "</div>";
                                        })
                                        ->html(),
                                ])
                                ->columns(3),
                        ];
                    }),

                Action::make('actualizarSistema')
                    ->label('Actualizar Hardware')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'En Mantenimiento')
                    ->modalHeading('Actualizar Componentes de Hardware')
                    ->modalDescription('Modifique los componentes técnicos de la computadora')
                    ->modalWidth('6xl')
                    ->modalSubmitActionLabel('Guardar Cambios')
                    ->modalCancelActionLabel('Cancelar')
                    ->form([

                        Grid::make(2)->schema([
                            Select::make('motherboard_component_id')
                                ->label('Placa Base')
                                ->options(function ($record) {
                                    $current = $record->components->firstWhere('componentable_type', 'App\Models\Motherboard');
                                    
                                    $available = Component::where('componentable_type', 'App\Models\Motherboard')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('computers')
                                        ->get()
                                        ->mapWithKeys(function ($component) {
                                            $mb = $component->componentable;
                                            return [$component->id => "{$mb->brand} {$mb->model} - Serial: {$component->serial}"];
                                        });

                                    if ($current) {
                                        $mb = $current->componentable;
                                        $available->prepend("{$mb->brand} {$mb->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->default(fn ($record) => $record->components->firstWhere('componentable_type', 'App\Models\Motherboard')?->id)
                                ->searchable()
                                ->live(),

                            Select::make('cpu_component_id')
                                ->label('Procesador (CPU)')
                                ->options(function (Get $get, $record) {
                                    $motherboardComponentId = $get('motherboard_component_id');
                                    $current = $record->components->firstWhere('componentable_type', 'App\Models\CPU');

                                    // Si no hay placa base seleccionada, mostrar el actual
                                    if (!$motherboardComponentId) {
                                        if ($current) {
                                            $cpu = $current->componentable;
                                            return [$current->id => "{$cpu->brand} {$cpu->model} - Serial: {$current->serial} (ACTUAL)"];
                                        }
                                        return [];
                                    }

                                    $mbComponent = Component::find($motherboardComponentId);
                                    if (!$mbComponent) {
                                        return [];
                                    }

                                    $motherboard = $mbComponent->componentable;
                                    $socket = $motherboard->socket;

                                    $available = Component::where('componentable_type', 'App\Models\CPU')
                                        ->where('status', 'Operativo')
                                        ->whereDoesntHave('computers')
                                        ->get()
                                        ->filter(function ($component) use ($socket) {
                                            return $component->componentable->socket === $socket;
                                        })
                                        ->mapWithKeys(function ($component) {
                                            $cpu = $component->componentable;
                                            return [$component->id => "{$cpu->brand} {$cpu->model} ({$cpu->socket}) - Serial: {$component->serial}"];
                                        });

                                    if ($current && $current->componentable->socket === $socket) {
                                        $cpu = $current->componentable;
                                        $available->prepend("{$cpu->brand} {$cpu->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                    }

                                    return $available;
                                })
                                ->default(fn ($record) => $record->components->firstWhere('componentable_type', 'App\Models\CPU')?->id)
                                ->searchable(),
                        ]),
                        Select::make('gpu_component_id')
                            ->label('Tarjeta Gráfica (GPU)')
                            ->options(function ($record) {
                                $current = $record->components->firstWhere('componentable_type', 'App\Models\GPU');
                                
                                $available = Component::where('componentable_type', 'App\Models\GPU')
                                    ->where('status', 'Operativo')
                                    ->whereDoesntHave('computers')
                                    ->get()
                                    ->mapWithKeys(function ($component) {
                                        $gpu = $component->componentable;
                                        return [$component->id => "{$gpu->brand} {$gpu->model} - {$gpu->vram}GB - Serial: {$component->serial}"];
                                    });

                                if ($current) {
                                    $gpu = $current->componentable;
                                    $available->prepend("{$gpu->brand} {$gpu->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                }

                                return $available;
                            })
                            ->default(fn ($record) => $record->components->firstWhere('componentable_type', 'App\Models\GPU')?->id)
                            ->searchable(),

                        Grid::make(2)->schema([
                        Repeater::make('rams')
                            ->label('Memorias RAM')
                            ->schema([
                                Select::make('component_id')
                                    ->label('RAM')
                                    ->options(function ($record) {
                                        // Obtener IDs de componentes RAM actualmente asignados a esta computadora
                                        $currentRamIds = $record->components()
                                            ->where('components.componentable_type', 'App\Models\RAM')
                                            ->pluck('components.id')
                                            ->toArray();
                                        
                                        // Obtener todos los componentes RAM operativos que:
                                        // 1. No están asignados a ninguna computadora (whereDoesntHave)
                                        // 2. O están asignados a ESTA computadora
                                        $availableRams = Component::where('componentable_type', 'App\Models\RAM')
                                            ->where('status', 'Operativo')
                                            ->where(function ($query) use ($currentRamIds) {
                                                $query->whereDoesntHave('computers')
                                                    ->orWhereIn('id', $currentRamIds);
                                            })
                                            ->get();
                                        
                                        return $availableRams->mapWithKeys(function ($component) use ($currentRamIds) {
                                            $ram = $component->componentable;
                                            $label = "{$ram->brand} {$ram->model} - {$ram->capacity}GB - Serial: {$component->serial}";
                                            if (in_array($component->id, $currentRamIds)) {
                                                $label .= " (ACTUAL)";
                                            }
                                            return [$component->id => $label];
                                        });
                                    })
                                    ->searchable()
                                    ->required()
                                    ->distinct(),
                            ])
                            ->minItems(1)
                            ->addActionLabel('Agregar RAM')
                            ->collapsible()
                            ->collapsed(),

                        Repeater::make('roms')
                            ->label('Almacenamiento')
                            ->schema([
                                Select::make('component_id')
                                    ->label('ROM')
                                    ->options(function ($record) {
                                        // Obtener IDs de componentes ROM actualmente asignados a esta computadora
                                        $currentRomIds = $record->components()
                                            ->where('components.componentable_type', 'App\Models\ROM')
                                            ->pluck('components.id')
                                            ->toArray();
                                        
                                        // Obtener todos los componentes ROM operativos que:
                                        // 1. No están asignados a ninguna computadora
                                        // 2. O están asignados a ESTA computadora
                                        $availableRoms = Component::where('componentable_type', 'App\Models\ROM')
                                            ->where('status', 'Operativo')
                                            ->where(function ($query) use ($currentRomIds) {
                                                $query->whereDoesntHave('computers')
                                                    ->orWhereIn('id', $currentRomIds);
                                            })
                                            ->get();
                                        
                                        return $availableRoms->mapWithKeys(function ($component) use ($currentRomIds) {
                                            $rom = $component->componentable;
                                            $label = "{$rom->brand} {$rom->model} - {$rom->capacity}GB - Serial: {$component->serial}";
                                            if (in_array($component->id, $currentRomIds)) {
                                                $label .= " (ACTUAL)";
                                            }
                                            return [$component->id => $label];
                                        });
                                    })
                                    ->searchable()
                                    ->required()
                                    ->distinct(),
                            ])
                            ->minItems(1)
                            ->addActionLabel('Agregar Almacenamiento')
                            ->collapsible()
                            ->collapsed(),
                                ]),
                        Grid::make(2)->schema
                        ([        
                        Select::make('power_supply_component_id')
                            ->label('Fuente de Poder')
                            ->options(function ($record) {
                                $current = $record->components->firstWhere('componentable_type', 'App\Models\PowerSupply');
                                
                                $available = Component::where('componentable_type', 'App\Models\PowerSupply')
                                    ->where('status', 'Operativo')
                                    ->whereDoesntHave('computers')
                                    ->get()
                                    ->mapWithKeys(function ($component) {
                                        $ps = $component->componentable;
                                        return [$component->id => "{$ps->brand} {$ps->model} - {$ps->power}W - Serial: {$component->serial}"];
                                    });

                                if ($current) {
                                    $ps = $current->componentable;
                                    $available->prepend("{$ps->brand} {$ps->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                }

                                return $available;
                            })
                            ->default(fn ($record) => $record->components->firstWhere('componentable_type', 'App\Models\PowerSupply')?->id)
                            ->searchable(),

                        Select::make('tower_case_component_id')
                            ->label('Gabinete/Case')
                            ->options(function ($record) {
                                $current = $record->components->firstWhere('componentable_type', 'App\Models\TowerCase');
                                
                                $available = Component::where('componentable_type', 'App\Models\TowerCase')
                                    ->where('status', 'Operativo')
                                    ->whereDoesntHave('computers')
                                    ->get()
                                    ->mapWithKeys(function ($component) {
                                        $case = $component->componentable;
                                        return [$component->id => "{$case->brand} {$case->model} - Serial: {$component->serial}"];
                                    });

                                if ($current) {
                                    $case = $current->componentable;
                                    $available->prepend("{$case->brand} {$case->model} - Serial: {$current->serial} (ACTUAL)", $current->id);
                                }

                                return $available;
                            })
                            ->default(fn ($record) => $record->components->firstWhere('componentable_type', 'App\Models\TowerCase')?->id)
                            ->searchable(),
                        ]),
                    ])
                    ->fillForm(function ($record): array {
                        return [
                            'motherboard_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\Motherboard')?->id,
                            'cpu_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\CPU')?->id,
                            'gpu_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\GPU')?->id,
                            'power_supply_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\PowerSupply')?->id,
                            'tower_case_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\TowerCase')?->id,
                            'network_adapter_component_id' => $record->components->firstWhere('componentable_type', 'App\Models\NetworkAdapter')?->id,
                            'rams' => $record->components
                                ->where('componentable_type', 'App\Models\RAM')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray(),
                            'roms' => $record->components
                                ->where('componentable_type', 'App\Models\ROM')
                                ->map(fn($c) => ['component_id' => $c->id])
                                ->toArray(),
                        ];
                    })
                    ->action(function ($record, array $data): void {
                        // Actualizar solo componentes internos de hardware
                        $componentData = [
                            'motherboard' => $data['motherboard_component_id'] ?? null,
                            'cpu' => $data['cpu_component_id'] ?? null,
                            'gpu' => $data['gpu_component_id'] ?? null,
                            'power_supply' => $data['power_supply_component_id'] ?? null,
                            'tower_case' => $data['tower_case_component_id'] ?? null,
                            'network_adapter' => $data['network_adapter_component_id'] ?? null,
                            'rams' => $data['rams'] ?? [],
                            'roms' => $data['roms'] ?? [],
                        ];

                        // Obtener componentes actuales para comparar
                        $currentComponents = [
                            'motherboard' => $record->components->firstWhere('componentable_type', 'App\Models\Motherboard')?->id,
                            'cpu' => $record->components->firstWhere('componentable_type', 'App\Models\CPU')?->id,
                            'gpu' => $record->components->firstWhere('componentable_type', 'App\Models\GPU')?->id,
                            'power_supply' => $record->components->firstWhere('componentable_type', 'App\Models\PowerSupply')?->id,
                            'tower_case' => $record->components->firstWhere('componentable_type', 'App\Models\TowerCase')?->id,
                            'network_adapter' => $record->components->firstWhere('componentable_type', 'App\Models\NetworkAdapter')?->id,
                            'rams' => $record->components->where('componentable_type', 'App\Models\RAM')->pluck('id')->toArray(),
                            'roms' => $record->components->where('componentable_type', 'App\Models\ROM')->pluck('id')->toArray(),
                        ];

                        // Identificar componentes que fueron REMOVIDOS (estaban antes pero ya no están)
                        $componentsToRemove = [];

                        // Componentes individuales
                        foreach (['motherboard', 'cpu', 'gpu', 'power_supply', 'tower_case', 'network_adapter'] as $type) {
                            $currentId = $currentComponents[$type];
                            $newId = $componentData[$type];
                            
                            // Si había uno y cambió (o se quitó), marcarlo como removido
                            if ($currentId && $currentId != $newId) {
                                $componentsToRemove[] = $currentId;
                            }
                        }

                        // RAMs - marcar como removidos los que ya no están en la lista
                        $newRamIds = array_column($componentData['rams'], 'component_id');
                        foreach ($currentComponents['rams'] as $currentRamId) {
                            if (!in_array($currentRamId, $newRamIds)) {
                                $componentsToRemove[] = $currentRamId;
                            }
                        }

                        // ROMs - marcar como removidos los que ya no están en la lista
                        $newRomIds = array_column($componentData['roms'], 'component_id');
                        foreach ($currentComponents['roms'] as $currentRomId) {
                            if (!in_array($currentRomId, $newRomIds)) {
                                $componentsToRemove[] = $currentRomId;
                            }
                        }

                        // Marcar SOLO los componentes que realmente fueron removidos
                        if (!empty($componentsToRemove)) {
                            $record->components()->updateExistingPivot($componentsToRemove, [
                                'status' => 'Removido',
                                'removed_by' => Auth::id(),
                            ]);
                        }

                        // Asignar nuevos componentes o actualizar los que se mantienen
                        $pivotData = [
                            'assigned_at' => now(),
                            'status' => 'Vigente',
                            'assigned_by' => Auth::id(),
                        ];

                        // Componentes individuales
                        $singleComponents = [
                            $componentData['motherboard'],
                            $componentData['cpu'],
                            $componentData['gpu'],
                            $componentData['power_supply'],
                            $componentData['tower_case'],
                            $componentData['network_adapter'],
                        ];

                        foreach ($singleComponents as $componentId) {
                            if ($componentId) {
                                $exists = $record->allComponents()->wherePivot('component_id', $componentId)->exists();
                                if ($exists) {
                                    $record->components()->updateExistingPivot($componentId, $pivotData);
                                } else {
                                    $record->components()->attach($componentId, $pivotData);
                                }
                            }
                        }

                        // RAMs
                        foreach ($componentData['rams'] as $ram) {
                            if (isset($ram['component_id'])) {
                                $exists = $record->allComponents()->wherePivot('component_id', $ram['component_id'])->exists();
                                if ($exists) {
                                    $record->components()->updateExistingPivot($ram['component_id'], $pivotData);
                                } else {
                                    $record->components()->attach($ram['component_id'], $pivotData);
                                }
                            }
                        }

                        // ROMs
                        foreach ($componentData['roms'] as $rom) {
                            if (isset($rom['component_id'])) {
                                $exists = $record->allComponents()->wherePivot('component_id', $rom['component_id'])->exists();
                                if ($exists) {
                                    $record->components()->updateExistingPivot($rom['component_id'], $pivotData);
                                } else {
                                    $record->components()->attach($rom['component_id'], $pivotData);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Hardware actualizado')
                            ->success()
                            ->body('Componentes internos actualizados exitosamente. Los periféricos se gestionan desde el módulo de Periféricos.')
                            ->send();
                    }),

                Action::make('verHistorial')
                    ->label('Historial')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->modalHeading('Historial de la Computadora')
                    ->modalDescription(fn ($record) => "Seleccione el tipo de historial que desea consultar para {$record->serial}")
                    ->modalIcon('heroicon-o-clock')
                    ->modalWidth('md')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->extraModalFooterActions([
                        Action::make('historialComponentes')
                            ->label('Historial de Componentes')
                            ->icon('heroicon-o-cpu-chip')
                            ->color('info')
                            ->url(fn ($record): string => route('filament.admin.resources.component-histories.index', [
                                'filters' => [
                                    'device_id' => ['value' => 'Computer-' . $record->id],
                                ],
                            ]))
                            ->openUrlInNewTab(),
                        
                        Action::make('historialMantenimientos')
                            ->label('Historial de Mantenimientos')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->color('warning')
                            ->url(fn ($record): string => route('filament.admin.resources.maintenances.index', [
                                'filters' => [
                                    'deviceable_type' => ['value' => 'App\Models\Computer'],
                                    'deviceable_id' => ['value' => $record->id],
                                ],
                            ]))
                            ->openUrlInNewTab(),
                        
                        Action::make('historialTraslados')
                            ->label('Historial de Traslados')
                            ->icon('heroicon-o-arrow-path')
                            ->color('success')
                            ->url(fn ($record): string => route('filament.admin.resources.transfers.index', [
                                'filters' => [
                                    'deviceable_type' => ['value' => 'App\Models\Computer'],
                                    'deviceable_id' => ['value' => $record->id],
                                ]
                                
                                ,
                            ]))
                            ->openUrlInNewTab(),
                        
                        Action::make('generarReporte')
                            ->label('Generar Reporte Completo')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('danger')
                            ->url(fn ($record): string => route('devices.full-report', [
                                'type' => 'computer',
                                'id' => $record->id,
                            ])),
                    ]),

                Action::make('desmantelar')
                    ->label('Desmantelar')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Desmantelar Computadora')
                    ->modalDescription(fn ($record) => "¿Está seguro de desmantelar la computadora {$record->serial}? Todos los componentes vigentes serán removidos y la computadora pasará al estado 'Desmantelado'.")
                    ->modalSubmitActionLabel('Sí, desmantelar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(fn ($record) => $record->status === 'Inactivo')
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // Actualizar todos los componentes vigentes a "Removido"
                            DB::table('componentables')
                                ->where('componentable_type', 'App\\Models\\Computer')
                                ->where('componentable_id', $record->id)
                                ->where('status', 'Vigente')
                                ->update([
                                    'status' => 'Removido',
                                    'updated_at' => now()
                                ]);
                            
                            // Cambiar el estado de la computadora a Desmantelado
                            $record->update(['status' => 'Desmantelado']);
                        });
                        
                        Notification::make()
                            ->title('Computadora desmantelada')
                            ->success()
                            ->body("La computadora {$record->serial} ha sido desmantelada exitosamente.")
                            ->send();
                    }),

                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay ningún registro de computadoras');
    }
}
