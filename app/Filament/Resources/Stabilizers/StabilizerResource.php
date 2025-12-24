<?php

namespace App\Filament\Resources\Stabilizers;

use App\Filament\Resources\Stabilizers\Pages\CreateStabilizer;
use App\Filament\Resources\Stabilizers\Pages\EditStabilizer;
use App\Filament\Resources\Stabilizers\Pages\ListStabilizers;
use App\Filament\Resources\Stabilizers\Schemas\StabilizerForm;
use App\Filament\Resources\Stabilizers\Tables\StabilizersTable;
use App\Models\Stabilizer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StabilizerResource extends Resource
{

    protected static ?string $model = Stabilizer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPower;

    protected static ?string $navigationLabel = 'Estabilizador';

    protected static ?string $modelLabel = 'Estabilizador';

    protected static ?string $pluralModelLabel = 'Estabilizadores';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Periféricos';

    protected static ?int $navigationSort = 43;

    public static function form(Schema $schema): Schema
    {
        return StabilizerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StabilizersTable::configure($table);
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
            'index' => ListStabilizers::route('/'),
            'create' => CreateStabilizer::route('/create'),
            'edit' => EditStabilizer::route('/{record}/edit'),
        ];
    }
}
