<?php

namespace App\Filament\Resources\TowerCases;

use App\Filament\Resources\TowerCases\Pages\CreateTowerCase;
use App\Filament\Resources\TowerCases\Pages\EditTowerCase;
use App\Filament\Resources\TowerCases\Pages\ListTowerCases;
use App\Filament\Resources\TowerCases\Schemas\TowerCaseForm;
use App\Filament\Resources\TowerCases\Tables\TowerCasesTable;
use App\Models\TowerCase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TowerCaseResource extends Resource
{

    protected static ?string $model = TowerCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Gabinete';

    protected static ?string $modelLabel = 'Gabinete';

    protected static ?string $pluralModelLabel = 'Gabinetes';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Periféricos';

    protected static ?int $navigationSort = 37;

    public static function form(Schema $schema): Schema
    {
        return TowerCaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TowerCasesTable::configure($table);
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
            'index' => ListTowerCases::route('/'),
            'create' => CreateTowerCase::route('/create'),
            'edit' => EditTowerCase::route('/{record}/edit'),
        ];
    }
}
