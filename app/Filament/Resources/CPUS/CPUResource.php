<?php

namespace App\Filament\Resources\CPUS;

use App\Filament\Resources\CPUS\Pages\CreateCPU;
use App\Filament\Resources\CPUS\Pages\EditCPU;
use App\Filament\Resources\CPUS\Pages\ListCPUS;
use App\Filament\Resources\CPUS\Schemas\CPUForm;
use App\Filament\Resources\CPUS\Tables\CPUSTable;
use App\Models\CPU;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CPUResource extends Resource
{

    protected static ?string $model = CPU::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $navigationLabel = 'Procesador';

    protected static ?string $modelLabel = 'Procesador';

    protected static ?string $pluralModelLabel = 'Procesadores';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Hardware';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return CPUForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CPUSTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCPUS::route('/'),
            'create' => CreateCPU::route('/create'),
            'edit' => EditCPU::route('/{record}/edit'),
        ];
    }
}
