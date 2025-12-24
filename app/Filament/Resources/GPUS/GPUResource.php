<?php

namespace App\Filament\Resources\GPUS;

use App\Filament\Resources\GPUS\Pages\CreateGPU;
use App\Filament\Resources\GPUS\Pages\EditGPU;
use App\Filament\Resources\GPUS\Pages\ListGPUS;
use App\Filament\Resources\GPUS\Schemas\GPUForm;
use App\Filament\Resources\GPUS\Tables\GPUSTable;
use App\Models\GPU;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GPUResource extends Resource
{

    protected static ?string $model = GPU::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CpuChip;

    protected static ?string $navigationLabel = 'Tarjeta Gráfica';

    protected static ?string $modelLabel = 'Tarjeta Gráfica';

    protected static ?string $pluralModelLabel = 'Tarjetas Gráficas';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Hardware';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return GPUForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GPUSTable::configure($table);
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
            'index' => ListGPUS::route('/'),
            'create' => CreateGPU::route('/create'),
            'edit' => EditGPU::route('/{record}/edit'),
        ];
    }
}
